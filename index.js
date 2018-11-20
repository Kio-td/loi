/* Conventions followed:
  -crockford.com/javascript/code.html
  -StandardJS
  -Standard Query Response (JSON) - json5/sqr.json5 (Kio)

 jshint -W002 esversion: 6 */
const ConfigStore = require('node-storage')
const config = new ConfigStore('./config/local.json')
const http = require('http')
const sendgrid = require('@sendgrid/mail')
const password = require('node-php-password')
const WebSocket = require('ws')
const mysql = require('mysql2')
const json = require('json5')
const url = require('url')
const uuid = require('crypto-random-string')
const httpServer = http.createServer({})
var errorData

// Global variables
// These usernames are banned from being registered, in part or in full.
const blacklist = ['all', 'ikaros', 'admin', 'console', 'sysadmin', 'owner', 'dev', 'developer', 'support', 'superuser', 'root', 'system', 'bot', 'npc']

// Webserver Sockets
const websocketConfig = { noServer: true } // The Websocket's default configuration.
// Create all the websockets here.
const main = new WebSocket.Server(websocketConfig) // The Main Websocket.
const chat = new WebSocket.Server(websocketConfig) // The Chat Service.
const battle = new WebSocket.Server(websocketConfig) // The socket dedicated to battles.
const anon = new WebSocket.Server(websocketConfig) // The Anonymous

// Set the API Key for the mail service.
sendgrid.setApiKey(config.get('int.sg'))

// Send Emails using the following template. Please leave this alone, it's fine as is.
function sendemail (to, template, data) {
  let msg = {
    to: to, // Supply the email.
    from: 'Legend of Ikaros <Noreply@legendofikaros.me>', // Supply the From Email.
    templateId: template, // Supply the template ID, created in Sendgrid.
    dynamic_template_data: data // Submit any Data that might be necessary.
  }
  sendgrid.send(msg) // Send the message.
}

// Function to check whether or not the user is authenticated.
// TODO: Full makeover of auth system from 1AUTH to Auth every request

function isconnected (req, ws) { // OUTDATED - NEED TO CONVERT TO checkToken().
  let ip = req.headers['x-forwarded-for'] // Get the NGINX "True IP".
  let uid = ip.replace(/\./g, '') // Create a UID by removing the dots of an IP.
  if (config.get('user.' + uid + '.token') === undefined) {
    ws.close(1013) //
  }
}

let connection = mysql.createPool(config.get('int.mysql')) // Create a MySQL2 Connection Pool.
console.log('Pool created - Server is running.')
// Writes errors to the logger.
function logToSQL (errorData, ip) {
  console.warn(errorData)
  connection.query('INSERT INTO `pagelog`(`eid`, `ip`, `page`, `toe`) VALUES (?,?,?,NOW())', ['SE_' + uuid(13), ip, json.stringify(errorData)]) // Use another connection from the pool to log the error data.
}

function checkToken (token, req, ws) {// eslint-disable-line
  var ret; var ip = req.headers['x-forwarded-for']
  connection.query('SELECT username, bal, guild, citid from users where token = ?', token, function (errorData, results) {
    if (errorData) { logToSQL(errorData, ip) } else if (results.length === 1) {
      ret = { auth: true, name: results[0].username, balance: results[0].bal, guildid: results[0].guild, city: results[0].citid }
    } else {
      ws.close(1013, 'MID TRAVEL FRAUD')
      ret = { auth: false }
    }
  })
  return ret
}

// Anonymous websocket, for getting general information. New races, pinging, and authentication.
anon.on('connection', function (ws, req) {
  let ip = req.headers['x-forwarded-for']

  try { ws.send(json.stringify({ ok: true, code: 3, msg: 'HI_ANON' })) } catch (errorData) { logToSQL(errorData, ip) } // Send the user a welcome message, log if there is an error.

  ws.on('message', function (messageData) { // When the client sends a message, run this.
    try {
      var jsonData = json.parse(messageData) // Try to parse JSON5 data if it's available.
    } catch (errorData) { try { ws.send(json.stringify({ ok: false, code: -3, msg: 'NOT_JSON5' })) } catch (errorData) { logToSQL(errorData, ip); return } return }
    switch (jsonData.cmd) {
      case undefined: // If there is no cmd variable, tell them there was an issue.
        try { ws.send(json.stringify({ ok: false, code: -1, msg: 'NO_COMMAND' })) } catch (errorData) { logToSQL(errorData, ip) }
        break
      case 'species': // Prints all the available species (Mainly for the signup list.)
        connection.query('select * from spec', function (errorData, results) {
          if (errorData) { logToSQL(errorData, ip); return }
          try { ws.send(json.stringify({ ok: true, code: 4, data: results })) } catch (errorData) { logToSQL(errorData, ip) }
        })
        break
      case 'authenticate': // Retrieve the token from the MySQL Server.
        if (jsonData.data === undefined) { // If there is no data field, throw a fit and end the process.
          try { ws.send(json.stringify({ ok: false, code: -3, msg: 'NO_DATA' })) } catch (errorData) { logToSQL(errorData, ip); return }
        } else {
          let credentials = jsonData.data
          credentials.username = credentials.username.toLowerCase() // Change the username to lowercase for compat reasons.
          connection.query('select ce, password, token from users where username = ?', [credentials.username], function (errorData, results) {
            if (errorData) { logToSQL(errorData, ip); return } else if (results.length === 0) { try { ws.send(json.stringify({ ok: false, code: -3, msg: 'NO_DATA' })) } catch (errorData) { logToSQL(errorData, ip); return } }
            var userData = results[0]
            if (userData.ce !== '0') { // If the email needs to be confirmed.
              try { ws.send(json.stringify({ ok: false, code: -4, msg: 'CONF_EMAIL' })) } catch (errorData) { logToSQL(errorData, ip) }
            } else if (password.verify(credentials.password, userData.password) === false) { // If the password does not match.
              try { ws.send(json.stringify({ ok: false, code: -4, msg: 'INC_PASS' })) } catch (errorData) { logToSQL(errorData, ip) }
            } else {
              try {
                ws.send(json.stringify({ ok: true, code: 4, data: Buffer.from(userData.token).toString('base64') }))
              } catch (errorData) { logToSQL(errorData, ip) }
            }
          })
        }
        return
      case 'authcallback': // Authentication for the callback service. Possibly will delete if it's not useful enough to me.
        if (jsonData.data === undefined) {
          try { ws.send(json.stringify({ ok: false, code: -3, msg: 'NO_DATA_FOUND' })) } catch (errorData) { logToSQL(errorData, ip); return }
        } else {
          var credentials = jsonData.data
          credentials.username = jsonData.un.toLowerCase()
          credentials.password = jsonData.pw
          connection.query('select uid, ce, password from users where username = ?', [credentials.username], function (errorData, results) {
            if (errorData) { logToSQL(errorData, ip); return }
            if (results.length === 0) { try { ws.send(json.stringify({ ok: false, code: -4, msg: 'NOBODY_FOUND' })) } catch (errorData) { logToSQL(errorData, ip) } } else {
              let userData = results[0]
              if (userData.ce !== '0') { try { ws.send(json.stringify({ ok: false, code: -4, msg: 'CONF_EMAIL' })) } catch (errorData) { logToSQL(errorData, ip) } } else if (password.verify(credentials.password, userData.password) === false) {
                try { ws.send(json.stringify({ ok: false, code: -4, msg: 'INC_PASS' })) } catch (errorData) { logToSQL(errorData, ip) }
              } else {
                let token = uuid(30)
                connection.query('INSERT INTO `oauthtokens`(`authid`, `uid`) VALUES (?,?)', [token, credentials.uid])
                try { ws.send(json.stringify({ ok: true, code: 4, data: { token: token, username: credentials.username } })) } catch (errorData) { logToSQL(errorData, ip) }
              }
            }
          })
        }
        return

      case 'ress2':
        if (jsonData.data === undefined) {
          try { ws.send(json.stringify({ ok: false, code: -3, msg: 'NO_DATA_FOUND' })) } catch (errorData) { logToSQL(errorData, ip); return }
        } else {
          let parsedData = jsonData.data
          if (parsedData.code === undefined || parsedData.password === undefined || parsedData.conpass === undefined) {
            try { ws.send(json.stringify({ ok: false, code: -3, msg: 'MISSING_DATA' })) } catch (errorData) { logToSQL(errorData, ip); return }
            return
          } else {
            connection.query('select uid from users where rs = ?', [parsedData.code], function (errorData, results) {
              if (errorData) { logToSQL(errorData, ip) } else if (results.length !== 1) {
                console.log(ip + " tried to access a code that doesn't exist.") // eslint ignore-line
                try { ws.send(json.stringify({ ok: false, code: -4, msg: 'FRAUD' })); return } catch (errorData) { logToSQL(errorData, ip) }
              } else if (parsedData.password !== parsedData.conpass) {
                try { ws.send(json.stringify({ ok: false, code: -4, msg: 'DIFFERENT' })); return } catch (errorData) { logToSQL(errorData, ip) }
              } else {
                connection.query('UPDATE users set password = ?, token = ?, rs = 0 where rs = ?', [password.hash(parsedData.password), uuid(30), parsedData.code], function (errorData) {
                  if (errorData) logToSQL(errorData, ip)
                  try { ws.send(json.stringify({ ok: true, code: 4, data: 'LOGIN_AGAIN' })) } catch (errorData) { logToSQL(errorData, ip) }
                })
              }
            })
          }
        }
        return
      case 'reset':
        if (jsonData.data === undefined) {
          try { ws.send(json.stringify({ ok: false, code: -3, msg: 'NO_DATA_FOUND' })) } catch (errorData) { logToSQL(errorData, ip) }
        } else {
          connection.query('select email from users where username = ?', [jsonData.data.toLowerCase()], function (errorData, results) {
            if (errorData) logToSQL(errorData, ip)
            else if (results.length !== 1) { try { ws.send(json.stringify({ ok: false, code: -3, msg: 'NO_USR' })) } catch (errorData) { logToSQL(errorData, ip) } } else {
              let token = uuid(30)
              connection.query('update users set rs=? where username=?', [token, jsonData.data], function (errorData) { if (errorData) logToSQL(errorData, ip) })
              sendemail(results['0'].email, 'd-3fcf2355b269462cb8941330ce44175f', { username: jsonData.data, url: 'https://legendofikaros.me/game/login?reset&code=' + token })
              try { ws.send(json.stringify({ ok: true, code: 4, msg: 'SENT_EMAIL' })) } catch (errorData) { logToSQL(errorData, ip) }
            }
          })
        }
        break
      case 'checkUsername':
        if (jsonData.data === undefined) {
          try { ws.send(json.stringify({ ok: false, code: -3, msg: 'NO_DATA_FOUND' })) } catch (errorData) { logToSQL(errorData, ip) }
        } else if (blacklist.includes(jsonData.data.toLowerCase())) {
          try { ws.send(json.stringify({ ok: false, code: -4, data: 'BL' })) } catch (errorData) { logToSQL(errorData, ip) }
        } else {
          connection.query('select username from users where username = ?', [jsonData.data.toLowerCase()], function (errorData, results) {
            if (errorData) { logToSQL(errorData, ip) } else if (results.length > 0) { try { ws.send(json.stringify({ ok: true, code: 4, data: 'F' })) } catch (errorData) { logToSQL(errorData, ip) } } else { try { ws.send(json.stringify({ ok: true, code: 4, data: 'NF' })) } catch (errorData) { logToSQL(errorData, ip) } }
          })
        }
        break
      case 'checkEmail':
        if (jsonData.data === undefined) {
          try { ws.send(json.stringify({ ok: false, code: -3, msg: 'NO_DATA_FOUND' })) } catch (errorData) { logToSQL(errorData, ip) }
        } else {
          connection.query('select username from users where email = ?', [jsonData.data], function (errorData, results) {
            if (errorData) logToSQL(errorData, ip)
            else if (results.length > 0) { try { ws.send(json.stringify({ ok: true, code: 4, data: false })) } catch (errorData) { logToSQL(errorData, ip) } } else { try { ws.send(json.stringify({ ok: true, code: 4, data: true })) } catch (errorData) { logToSQL(errorData, ip) } }
          })
        }
        break
      case 'register':
        if (jsonData.data === undefined) {
          try { ws.send(json.stringify({ ok: false, code: -3, msg: 'NO_DATA_FOUND' })) } catch (errorData) { logToSQL(errorData, ip) }
        } else {
          let userInfo = jsonData.data
          userInfo.username = userInfo.username.toLowerCase()
          userInfo.email = userInfo.email.toLowerCase()
          connection.query('select username from users where username = ? or email = ?', [userInfo.username, userInfo.email], function (a, b) {
            if (b.length > 0) { try { ws.send(json.stringify({ ok: false, code: 6, msg: 'ACCT_EXISTS' })) } catch (errorData) { logToSQL(errorData, ip) } } else if (blacklist.includes(userInfo.username)) { try { ws.send(json.stringify({ ok: false, code: 6, msg: 'ACCT_BLACKLIST' })) } catch (errorData) { logToSQL(errorData, ip) } } else {
              let token = uuid(30)
              let confirmEmail = uuid(30)
              connection.query('INSERT INTO `users`(`username`, `password`, `email`, `token`, `ce`, `spid`) VALUES (?,?,?,?,?,?);', [userInfo.username, password.hash(userInfo.password), userInfo.email, token, confirmEmail, userInfo.sp], function (errorData) {
                if (errorData) logToSQL(errorData, ip)
                sendemail(userInfo.email, 'd-01419621eb244bd29bb43c34fcd6b5dd', { username: userInfo.username, url: 'https://legendofikaros.me/game/login?confirm=' + confirmEmail + '&username=' + userInfo.username })
                try { ws.send(json.stringify({ ok: true, code: 4, msg: 'CHECK_EMAIL' })) } catch (errorData) { logToSQL(errorData, ip) }
                console.log(ip + ' has registered user ' + userInfo.username + '. Species: ' + userInfo.sp)
              })
            }
          })
        }
        break
      default:
        try { ws.send(json.stringify({ ok: false, code: -3, msg: 'NO_DATA_FOUND' })) } catch (errorData) { logToSQL(errorData, ip) }
        break
    }
  })
})

// Main Websocket, for processing most events related to the game
main.on('connection', function (ws, req) {
  let ip = req.headers['x-forwarded-for']
  let uid = ip.replace(/\./g, '')

  console.log(ip + ' has connected.')
  if (config.get('user.' + uid + '.dontdecon')) {
    console.log(ip + ' has been recognized.')
    config.remove('user.' + uid + '.dontdecon')
  }
  ws.on('close', function dc (code, reason) {
    console.log(ip + ' has disconnected.')
    if (!config.get('user.' + uid + '.dontdecon')) {
      config.remove('user.' + uid)
    }
  })
  if (config.get('user.' + uid) === undefined) {
    try { ws.send('{code:1}') } catch (errorData) { logToSQL(errorData, ip) }
    ws.on('message', function incoming (data) {
      // user is authenticated
      if (typeof config.get('user.' + uid) !== 'undefined') {
        let jsonData = json.parse(data)

        // Main commands for the main server.
        switch (jsonData.cmd) {
          case undefined:
            try { ws.send(json.stringify({ ok: false, msg: 'cmd_not_found', code: -1 })) } catch (errorData) { logToSQL(errorData, ip) }
            return

          case 'dontdecon':
            config.put('user.' + uid + '.dontdecon', true)
            try { ws.send(json.stringify({ ok: true, msg: 'SO_REMEMBER_ME_AND_I_WILL_REMEMBER_YOU', code: 2152 })) } catch (errorData) { logToSQL(errorData, ip) }
            break
          case 'charge':
          // Todo - Charge fee and return ticket, which will be used to later process in the client.
        }
      } else {
        let jsonData = json.parse(data)
        if (jsonData.atoken === undefined) ws.close(1013)
        else {
          connection.query('SELECT username, bal from users where token = ?', [jsonData.atoken], function asdf (a, b) {
            if (b.length === 1) {
              config.put('user.' + uid + '.token', jsonData.atoken)
              config.put('user.' + uid + '.un', b[0].username)
              console.log(ip + ' identified as ' + b[0].username + '.')
              try { ws.send(json.stringify({ ok: true, msg: 'I_THOUGHT_I_REMEMBERED_YOU_OWO', code: 5 })) } catch (errorData) { logToSQL(errorData, ip) }
            } else ws.close(1013)
          })
        }
      }
    })
  } else {
    try { ws.send('{"code":2}') } catch (errorData) { logToSQL(errorData, ip) }
  }
})

// Chat websocket, for.. chatting.
chat.on('connection', function connection (ws, req) {
  isconnected(req, ws)
  ws.on('message', function msg (chatData) {
    let ip = req.headers['x-forwarded-for']
    let uid = ip.replace(/\./g, '')
    chatData = chatData.split(/\r?\n|\r/g)[0].replace(/</g, '&lt;').replace(/>/g, '&gt;').trim() // Make sure no true HTMl is passed into the server.
    if (chatData.length >= 80) { // If the message is over 80 characters, whisper to the character that the message has been voided.
      try { ws.send(json.stringify({ ok: false, display: '*Your voice falls on deaf ears. (Too many characters.)', color: 'red' })) } catch (errorData) { logToSQL(errorData, ip) }
      return
    }

    switch (chatData.split(' ')[0]) {
      case '!s':
        try { ws.send(json.stringify({ ok: false, display: '*Shouts are not setup yet.', color: 'red' })) } catch (errorData) { logToSQL(errorData, ip) }
        return

      case '!g':
        try { ws.send(json.stringify({ ok: false, display: '*Guilds are not setup yet.', color: 'red' })) } catch (errorData) { logToSQL(errorData, ip) }
        return

      case '!f':
        if (chatData.split(' ')[1].toLowerCase() === 'all') {
          connection.query('select token from users where uid in (select ut from friends where uf in (select uid from users where token = ?)) or uid in (select uf from friends where ut in (select uid from users where token = ?)) and uid not in (select uid from users where token = ?)', [config.get('user.' + uid + '.token'), config.get('user.' + uid + '.token'), config.get('user.' + uid + '.token')], function (c, d) {
            if (c) logToSQL(c, ip)
            d.forEach(function (h) {
              chat.clients.forEach(function each (client) {
                if (client.readyState === WebSocket.OPEN && config.get('user.' + uid + '.token') === h.token) {
                  connection.query('SELECT citid from users where token = ?', [config.get('user.' + client._socket.remoteAddress.replace(/::ffff:/g, '').replace(/\./g, '') + '.token')], function a (e, f) {
                    uid = req.connection.remoteAddress.replace(/::ffff:/g, '').replace(/\./g, '')
                    if (errorData) logToSQL(errorData, ip)
                    if (f.length === 1) { try { client.send(json.stringify({ ok: true, display: config.get('user.' + uid + '.un') + '>> ' + chatData.replace('!f all ', ''), color: 'pink' })) } catch (errorData) { logToSQL(errorData, ip) } }
                  })
                }
              })
            })
            try { ws.send(json.stringify({ ok: true, display: config.get('user.' + uid + '.un') + '>> ' + chatData.replace('!f all ', ''), color: 'pink' })) } catch (errorData) { logToSQL(errorData, ip) }
          })
        } else {
          let friendUsername = chatData.split(' ')[1].toLowerCase()
          if (friendUsername === config.get('user.' + uid + '.un')) { try { ws.send(json.stringify({ ok: false, display: 'You cannot send a message to yourself.' })) } catch (errorData) { logToSQL(errorData, ip) } } else {
            connection.query('select token from users where username = ? and uid in ( select ut from friends where uf in (select uid from users where token = ?) ) or uid in ( select uf from friends where ut in (select uid from users where token = ?) )', [friendUsername, config.get('user.' + uid + '.token'), config.get('user.' + uid + '.token')], function (a, b) {
              if (b.length === 0) { try { ws.send(json.stringify({ ok: false, display: friendUsername + ' is not a friend of yours. Are you sure you typed their name correctly?' })) } catch (errorData) { logToSQL(errorData, ip) } } else {
                chat.clients.forEach(function each (client) {
                  if (client.readyState === WebSocket.OPEN) {
                    connection.query('SELECT citid from users where token = ?', [config.get('user.' + client._socket.remoteAddress.replace(/::ffff:/g, '').replace(/\./g, '') + '.token')], function a (a, e) {
                      uid = req.connection.remoteAddress.replace(/::ffff:/g, '').replace(/\./g, '')
                      if (errorData) logToSQL(errorData, ip)
                      if (b.length === 1) {
                        try { client.send(json.stringify({ ok: true, display: config.get('user.' + uid + '.un') + '>> ' + chatData.replace('!f ' + friendUsername + ' ', ''), color: 'pink' })) } catch (errorData) { logToSQL(errorData, ip) }
                        try { ws.send(json.stringify({ ok: true, display: config.get('user.' + uid + '.un') + '>> ' + chatData.replace('!f ' + friendUsername + ' ', ''), color: 'pink' })) } catch (errorData) { logToSQL(errorData, ip) }
                      }
                    })
                  }
                })
              }
            })
          }
        }
        return

      case '!p':
        try { ws.send(json.stringify({ ok: false, display: '*Party Talking is not setup yet.', color: 'red' })) } catch (errorData) { logToSQL(errorData, ip) }
        return

      default:
        chat.clients.forEach(function each (client) {
          if (client.readyState === WebSocket.OPEN) {
            connection.query('SELECT citid from users where token = ?', [config.get('user.' + client._socket.remoteAddress.replace(/::ffff:/g, '').replace(/\./g, '') + '.token')], function a (a, b) {
              uid = req.connection.remoteAddress.replace(/::ffff:/g, '').replace(/\./g, '')
              if (errorData) logToSQL(errorData, ip)
              if (b.length === 1) { try { client.send(json.stringify({ ok: true, display: config.get('user.' + uid + '.un') + '>> ' + chatData })) } catch (errorData) { logToSQL(errorData, ip) } }
            })
          }
        })
    }
  })
})

// Battlesocket, for processing battling sequences
battle.on('connection', function (ws, req) {
  let pppp = false
  let ip = req.headers['x-forwarded-for']
  let uid = ip.replace(/\./g, '')
  if (config.get('user.' + uid + 'battleid')) { try { ws.send(json.stringify({ ok: true, code: 2, bid: config.get('user.' + uid + '.battleid'), msg: 'YOU_ARE_STILL_IN_A_FIGHT' })) } catch (errorData) { logToSQL(errorData, ip) } } else {
    let r = uuid(7)
    connection.query('select uid from users where token = ?', [config.get('user.' + uid + '.token')], function (errorData, b) {
      pppp = b[0].uid
    })
    console.log(config.get('user.' + uid + '.token'))
    connection.query("SELECT * from monster where towns LIKE CONCAT('%', (select citid from users where token = ? ), '%') order by RAND() limit 1;", [config.get('user.' + uid + '.token')], function (errorData, b) {
      if (errorData) logToSQL(errorData, ip)
      connection.query('INSERT INTO currentbattles (battleid, uid, mid, phealth, mhealth) VALUES (?, ?, ?, ?, ?)', [r, pppp, b[0].uid, 0, b[0].hp])
      try {
        ws.send(
          json.stringify({ battleid: r, story: 'STORY FOR BATTLEBOX', mobdata: { name: b[0].name, health: 5, ttlhealth: b[0].hp }, playerdata: { name: 'PLAYERNAME', health: 5, ttlhealth: 10, inventory: [], effects: [], canfight: true, istripped: false, isproceed: false, isabletoflee: true } }))
      } catch (errorData) { logToSQL(errorData, ip) }
    })
  }
})

// Main server module. change anything here and I will kill you.
httpServer.on('upgrade', function upgrade (request, socket, head) {
  const pathname = url.parse(request.url).pathname
  switch (pathname) {
    case '/main':
      main.handleUpgrade(request, socket, head, function done (ws) {
        main.emit('connection', ws, request)
      })
      break

    case '/chat':
      chat.handleUpgrade(request, socket, head, function done (ws) {
        chat.emit('connection', ws, request)
      })
      break
    case '/btl':
      battle.handleUpgrade(request, socket, head, function done (ws) {
        battle.emit('connection', ws, request)
      })
      break
    case '/anon':
      anon.handleUpgrade(request, socket, head, function done (ws) {
        anon.emit('connection', ws, request)
      })
      break
    default:
      socket.destroy()
  }
})

// Main port for server.
httpServer.listen(2053)
