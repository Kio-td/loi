const configStore = require('node-storage');
const config = new configStore("./config/local.json"); //Configuration for Server, and for users
const http = require('http');
const sendgrid = require('@sendgrid/mail');
const password = require('node-php-password');
const WebSocket = require('ws');
const mysql = require('mysql2');
const json = require('json5')
const url = require('url');
const uuid = require('crypto-random-string');
const httpServer = http.createServer({});

//Global variables
//These usernames are banned from being registered, in part or in full.
const websocketConfig = new Object({noServer: true});
const blacklist = ["all", "ikaros", "admin", "console", "sysadmin", "owner", "dev", "developer", "support", "superuser", "root", "system", "bot", "npc"];

//Webserver Sockets
const main = new WebSocket.Server(websocketConfig);
const chat = new WebSocket.Server(websocketConfig);
const battle = new WebSocket.Server(websocketConfig);
const anon = new WebSocket.Server(websocketConfig);


sendgrid.setApiKey(config.get("int.sg"));

//Send Emails using the following template. Please leave this alone, it's fine as is.
function sendemail(to, template, data) {
	msg = {
		to: to,
		from: "Legend of Ikaros <Noreply@loi.nayami.party>",
		templateId: template,
		dynamic_template_data: data
	}
	sendgrid.send(msg);
}

//Function to check whether or not the user is authenticated.
//TODO: Full makeover of auth system from 1AUTH to Auth every request

function isconnected(req, ws) {
	let ip = req.headers['x-forwarded-for']
	let uid = ip.replace(/\./g, '');
	if (config.get("user." + uid + ".token") == undefined) {
		ws.close(1013);
	}
}

let connection = mysql.createPool(config.get("int.mysql"));
console.log("Pool created - Server is running.");
	//Writes errors to the logger.
	function pdc(error, ip) {
		console.warn(error);
			connection.query("INSERT INTO `pagelog`(`eid`, `ip`, `page`, `toe`) VALUES (?,?,?,NOW())", ["SE_"+uuid(13), ip, json.stringify(error)]);
	}


	//, req, ws
	function checkToken(token) {
		var ret;
		//let ip = req.headers['x-forwarded-for'];
		connection.query("SELECT username, bal, guild, citid from users where token = ?", token, function (error, results) {
			//if (error) {pdc(error, ip);}
			//else
			if (results.length == 1) {
				ret = {auth: true, name: results[0].username, balance: results[0].bal, guildid: results[0].guild, city: results[0].citid};
			} else {
				console.log(false)
				//ws.close(1013, "MID TRAVEL FRAUD");
				ret = {auth: false}
			}
		});
		return ret;
	}
	throw checkToken("$2y$10$wb1Nz.X4dMCd8kEdpWA3QeUTv.itHBRrX0RsYyO.OCZrQRamtuS3q");



//Anonymous websocket, for getting general information. New races, pinging, and authentication.
	anon.on('connection', function(ws, req) {

		let ip = req.headers['x-forwarded-for'];
		let uid = ip.replace(/\./g, '');


		try{ws.send(json.stringify({ok:true, code:3, msg:"HI_ANON"}));} catch (e) {pdc(e, ip);}
		ws.on('message', function(data) {
			try {
				d = json.parse(data);
			} catch (e) {
				try{ws.send(json.stringify({ok:false, code:-3, msg:"NOT_JSON5"}));} catch (e) {pdc(e, ip);}
				return;
			}
			if (d["cmd"] == undefined) {
				try{ws.send(json.stringify({ok:false, code:-1, msg:"NO_CMD"}));} catch (e) {pdc(e, ip);}
				return;
			} else if (d["cmd"] == "species") { //Return all the species that is available for use
				connection.query("select * from spec", function(error,b) {
					if(a) pdc(a,  ip);
					try{ws.send(json.stringify({ok:true, code:4, data:b}));} catch (e) {pdc(e,  ip);}
				});
			} else if (d["cmd"] == "auth") { //Authenticate the user.
				if(d["data"] == undefined) {
					try{ws.send(json.stringify({ok:false, code:-3, msg:"NO_DATA_FOUND"}));} catch (e) {pdc(e, ip);}
				} else {
					data = d["data"]
					//un, pw
					data.un = data.un.toLowerCase();
					data.pw = data.pw;
					connection.query("select ce, password, token from users where username = ?", [data.un], function(error,b) {
						if (error) pdc(error, ip);
						if(b.length == 0) {try{ws.send(json.stringify({ok:false, code:-4, msg:"NOBODY_FOUND"}))} catch (e) {pdc(e, ip);}} else {
							s = b[0];
							if(s.ce !== "0") {try{ws.send(json.stringify({ok:false, code:-4, msg:"CONF_EMAIL"}));} catch (e) {pdc(e, ip);}}
							else if(password.verify(data.pw, s.password) == false) {
								try{ws.send(json.stringify({ok:false, code:-4, msg:"INC_PASS"}));} catch (e) {pdc(e, ip);}
							} else {
								try{ws.send(json.stringify({ok:true, code:4, data:Buffer.from(s.token).toString('base64')}));} catch (e) {pdc(e, ip);}
							}
						}
					})
				}
			} else if (d["cmd"] == "authcallback") { //Used for the callback page.
				if(d["data"] == undefined) {
					try{ws.send(json.stringify({ok:false, code:-3, msg:"NO_DATA_FOUND"}));} catch (e) {pdc(e, ip);}
				} else {
					data = d["data"]
					//un, pw
					data.un = data.un.toLowerCase();
					data.pw = data.pw;
					connection.query("select uid, ce, password from users where username = ?", [data.un], function(error,b) {
						if (error) pdc(error, ip);
						if(b.length == 0) {try{ws.send(json.stringify({ok:false, code:-4, msg:"NOBODY_FOUND"}))} catch (e) {pdc(e, ip);}} else {
							s = b[0];
							if(s.ce !== "0") {try{ws.send(json.stringify({ok:false, code:-4, msg:"CONF_EMAIL"}));} catch (e) {pdc(e, ip);}}
							else if(password.verify(data.pw, s.password) == false) {
								try{ws.send(json.stringify({ok:false, code:-4, msg:"INC_PASS"}));} catch (e) {pdc(e, ip);}
							} else {
								token = uuid(30);
								connection.query("INSERT INTO `oauthtokens`(`authid`, `uid`) VALUES (?,?)", [token, s.uid]);
								try{ws.send(json.stringify({ok:true, code:4, data:{token: token, username:data.un}}));} catch (e) {pdc(e, ip);}
							}
						}
					})
				}
			} else if (d["cmd"] == "ress2") { //Phase 2 of the password reset function.
				if(d["data"] == undefined) {
					try{ws.send(json.stringify({ok:false, code:-3, msg:"NO_DATA_FOUND"}));} catch (e) {pdc(e, ip);}
				} else {
					try {
						m = d["data"]
					} catch (e) {
						try{ws.send(json.stringify({ok:false, code:-3, msg:"NOT_JSON5"}));} catch (e) {pdc(e, ip);}
						return;
					}
					if(m["code"] == undefined || m["password"] == undefined || m["conpass"] == undefined) {
						try{ws.send(json.stringify({ok: false, code:-3, msg:"MISSING_DATA"}));} catch (e) {pdc(e, ip);}
						return
					}
					connection.query("select uid from users where rs = ?", [m["code"]], function(a, b) {
						if (error) pdc(error, ip);
						if (b.length != 1) {
							try{ws.send(json.stringify({ok:false, code:-4, msg:"FRAUD"}));} catch (e) {pdc(e, ip);}
							console.log(ip + " tried to access a code that doesn't exist.");
						} else {
							if (m["password"] != m["conpass"]) {try{ws.send(json.stringify({ok: false, code: -4, msg: "DIFFERENT"}))} catch (e) {pdc(e, ip);}}
							else {
								connection.query("UPDATE users set password = ?, token = ?, rs = 0 where rs = ?", [password.hash(m["password"]), uuid(30), m["code"]], function(a, b) {
									if (error) pdc(error, ip);
									try{ws.send(json.stringify({ok:true, code:4, data:"LOGIN_AGAIN"}));} catch (e) {pdc(e, ip);}
								});
							}
						}
					});
				}
			} else if (d["cmd"] == "reset") { //Reset passwords.
				if(d["data"] == undefined) {
					try{ws.send(json.stringify({ok:false, code:-3, msg:"NO_DATA_FOUND"}));} catch (e) {pdc(e, ip);}
				} else {
					connection.query("select email from users where username = ?", [d["data"].toLowerCase()], function (a, b) {
						if (error) pdc(error, ip);
						if (b.length != 1) {try{ws.send(json.stringify({ok:false, code:-3, msg:"NO_USR"}))} catch (e) {pdc(e, ip);}}
						else {
							token = uuid(30);
							connection.query("update users set rs=? where username=?", [token, d["data"]], function(a) {
								if (error) pdc(error, ip);
							});
							sendemail(b["0"].email, "d-3fcf2355b269462cb8941330ce44175f", {username: d["data"], url: "https://loi.nayami.party/game/login?reset&code="+token});
							try{ws.send(json.stringify({ok: true, code: 4, msg: "SENT_EMAIL"}));} catch (e) {pdc(e, ip);}
						}
					});
				}
			} else if (d["cmd"] == "cun") { //Check usernames to make sure they're not banned/registered
				if(d["data"] == undefined) {
					try{ws.send(json.stringify({ok:false, code:-3, msg:"NO_DATA_FOUND"}));} catch (e) {pdc(e, ip);}
				} else if (blacklist.includes(d["data"].toLowerCase())) {
					try{ws.send(json.stringify({ok:false, code:-4, data:"BL"}))} catch (e) {pdc(e, ip);}
				} else {
					connection.query("select username from users where username = ?", [d["data"].toLowerCase()], function (a, b) {
						if (error) pdc(error, ip);
						if (b.length > 0) {try{ws.send(json.stringify({ok:true, code:4, data:"F"}));} catch (e) {pdc(e, ip);}}
						else {try{ws.send(json.stringify({ok:true, code:4, data:"NF"}));} catch (e) {pdc(e, ip);}}
					});
				}
			} else if (d["cmd"] == "cem") {
				if(d["data"] == undefined) {
					try{ws.send(json.stringify({ok:false, code:-3, msg:"NO_DATA_FOUND"}));} catch (e) {pdc(e, ip);}
				} else {
					connection.query("select username from users where email = ?", [d["data"]], function (a, b) {
						if (error) pdc(error, ip);
						if (b.length > 0) {try{ws.send(json.stringify({ok:true, code:4, data:false}));} catch (e) {pdc(e, ip);}}
						else {try{ws.send(json.stringify({ok:true, code:4, data:true}));} catch (e) {pdc(e, ip);}}
					});
				}
			} else if (d["cmd"] == "create") {
				if(d["data"] == undefined) {
					try{ws.send(json.stringify({ok:false, code:-3, msg:"NO_DATA_FOUND"}));} catch (e) {pdc(e, ip);}
				} else {
					n = d.data;
					//un, em, pw, sp
					n.un = n.un.toLowerCase();
					n.em = n.em.toLowerCase();
					connection.query("select username from users where username = ? or email = ?", [n.un, n.em], function (a, b) {
						if(b.length > 0) {try{ws.send(json.stringify({ok: false, code:6, msg: "ACCT_EXISTS"}))} catch (e) {pdc(e, ip);}}
						else if (blacklist.includes(n.un.toLowerCase()))  {try{ws.send(json.stringify({ok: false, code:6, msg: "ACCT_BLACKLIST"}))} catch (e) {pdc(e, ip);}}
						else {
							token = uuid(30);
							ce = uuid(30);
							connection.query("INSERT INTO `users`(`username`, `password`, `email`, `token`, `ce`, `spid`) VALUES (?,?,?,?,?,?);", [n.un, password.hash(n.pw),n.em, token, ce, n.sp], function (a) {
								if (error) pdc(error, ip);
								sendemail(n.em, "d-01419621eb244bd29bb43c34fcd6b5dd", {username: n.un, url: "https://loi.nayami.party/game/login?confirm=" + ce + "&username=" + n.un});
								try{ws.send(json.stringify({ok:true, code:4, msg:"CHECK_EMAIL"}));} catch (e) {pdc(e, ip);}
							});
						}
					});
				}
			}
		});
	});

//Main Websocket, for processing most events related to the game
	main.on('connection', function connection(ws, req) {


		let ip = req.headers['x-forwarded-for']
		let uid = ip.replace(/\./g, '');


		console.log(ip + " has connected.");
		if (config.get("user." + uid + ".dontdecon")) {
			console.log(ip + " has been recognized.");
			config.remove("user." + uid + ".dontdecon");
		}
		ws.on('close', function dc(code, reason) {
			console.log(ip + " has disconnected.");
			if (!config.get("user." + uid + ".dontdecon")) {
				config.remove("user." + uid);
			}
		});
		if (config.get("user." + uid) == undefined) {
			try{ws.send('{code:1}');} catch (e) {pdc(e, ip);}
			ws.on('message', function incoming(data) {
				//user is authenticated
				if (typeof config.get("user." + uid) !== "undefined") {
					try {
						jsonData = json.parse(data);
					} catch (e) {
						try{ws.send(json.stringify({
							ok: false,
							msg: "not_JSON5",
							code: -2
						}));} catch (e) {pdc(e, ip);}
						return;
					}
					if (jsonData["cmd"] == undefined) {
						try{ws.send(json.stringify({ ok: false, msg: "cmd_not_found", code: -1 }));} catch (e) {pdc(e, ip);}
						return;
					} else {
						if (jsonData["cmd"] == "dontdecon") {
							config.put("user." + uid + ".dontdecon", true);
							try{ws.send(json.stringify({
								ok: true,
								msg: "SO_REMEMBER_ME_AND_I_WILL_REMEMBER_YOU",
								code: 2152
							}));} catch (e) {pdc(e, ip);}
							return;
						} else if (jsonData["cmd"] == "charge") {

						}
					}
				} else {
					try { jsonData = json.parse(data); } catch (e) { ws.close(1013); return; }
					if (jsonData["atoken"] == undefined) ws.close(1013)
					else {
						let rows = 0
						connection.query("SELECT username, bal from users where token = ?", [jsonData["atoken"]], function asdf(a, b) {
							if (b.length == 1) {
								config.put("user." + uid + ".token", jsonData["atoken"]);
								config.put("user." + uid + ".un", b[0].username);
								console.log(ip + " identified as " + b[0].username + ".");
								try{ws.send(json.stringify({ ok: true, msg: "I_THOUGHT_I_REMEMBERED_YOU_OWO", code: 5 }));} catch (e) {pdc(e, ip);}
							} else ws.close(1013);
						});
					}
				}
			});
		} else {
			try{ws.send('{"code":2}');} catch (e) {pdc(e, ip);}
		}
	});

//Chat websocket, for.. chatting.
	chat.on('connection', function connection(ws, req) {
		isconnected(req, ws);
		ws.on('message', function msg(data) {
			let ip = req.headers['x-forwarded-for'];
			let uid = ip.replace(/\./g, '');
			ds = 0; //Start with Data Sanatized is Okay.
			data = data.split(/\r?\n|\r/g)[0].replace(/\</g, '&lt;').replace(/\>/g, '&gt;').trim(); //Make sure no true HTMl is passed into the server.
			if (data.length >= 80) { // If the message is over 80 characters, whisper to the character that the message has been voided.
				try{ws.send(json.stringify({ ok: false, display: "*Your voice falls on deaf ears. (Too many characters.)", color: "red" }));} catch (e) {pdc(e, ip);}
				ds = 1
			}
			if (ds == 0) {
				if (data.split(" ")[0] == "!s") { //Shouting, if this is ommitted then only the people in your town will hear you.
					try{ws.send(json.stringify({ ok: false, display: "*Shouts are not setup yet.", color: "red" }));} catch (e) {pdc(e, ip);}
				} else if (data.split(" ")[0] == "!g") { //Send a message to your guild. This will essentially shout to all of your guild. Not going to setup ATM.
				} else if (data.split(" ")[0] == "!f") { //Send a message to your friends
					if (data.split(" ")[1] == "all") {
						connection.query("select uid from users where token = ?", [config.get("user." + uid + ".token")], function(a, b) {
							connection.query("select token from users where uid in (select ut from friends where uf = ?) or uid in (select uf from friends where ut = ?) and ? != uid", [b[0].uid, b[0].uid, b[0].uid], function(c, d) {
								if (c) pdc(c, ip);
								d.forEach(function(h) {
									chat.clients.forEach(function each(client) {
										if (client.readyState === WebSocket.OPEN) {
											if (config.get("user." + client._socket.remoteAddress.replace(/::ffff:/g, '').replace(/\./g, '') + ".token") == h.token) {
												connection.query("SELECT citid from users where token = ?", [config.get("user." + client._socket.remoteAddress.replace(/::ffff:/g, '').replace(/\./g, '') + ".token")], function a(e, f) {
													uid = req.connection.remoteAddress.replace(/::ffff:/g, '').replace(/\./g, '');
													if (e) pdc(e, ip);
													if (f.length == 1) { try{client.send(json.stringify({ ok: true, display: config.get("user." + uid + ".un") + ">> " + data.replace("!f all ", ""), color: "pink" }));} catch (e) {pdc(e, ip);} }
												});
											}
										}
									});
								});
								try{ws.send(json.stringify({ ok: true, display: config.get("user." + uid + ".un") + ">> " + data.replace("!f all ", ""), color: "pink" }));} catch (e) {pdc(e, ip);}
							});
						});
					} else {
						fun = data.split(" ")[1].toLowerCase();
						if (fun == config.get("user." + uid + ".un")) { try{ws.send(json.stringify({ ok: false, display: "You cannot send a message to yourself." }))} catch (e) {pdc(e, ip);} } else {

							connection.query("select uid from users where token = ?", [config.get("user." + uid + ".token")], function(f, g) {
								connection.query("select token from users where username = ? and uid in ( select ut from friends where uf = ? ) or uid in ( select uf from friends where ut = ? )", [fun, g[0].uid, g[0].uid], function(a, b) {
									if (b.length == 0) { try{ws.send(json.stringify({ ok: false, display: fun + " is not a friend of yours. Are you sure you typed their name correctly?" }))} catch (e) {pdc(e, ip);} } else {
										chat.clients.forEach(function each(client) {
											if (client.readyState === WebSocket.OPEN) {
												connection.query("SELECT citid from users where token = ?", [config.get("user." + client._socket.remoteAddress.replace(/::ffff:/g, '').replace(/\./g, '') + ".token")], function a(a, e) {
													uid = req.connection.remoteAddress.replace(/::ffff:/g, '').replace(/\./g, '');
													if (error) pdc(error, ip);
													if (b.length == 1) {
														try{client.send(json.stringify({ ok: true, display: config.get("user." + uid + ".un") + ">> " + data.replace("!f " + fun + " ", ""), color: "pink" }));} catch (e) {pdc(e, ip);}
														try{ws.send(json.stringify({ ok: true, display: config.get("user." + uid + ".un") + ">> " + data.replace("!f " + fun + " ", ""), color: "pink" }));} catch (e) {pdc(e, ip);}
													}
												});
											}
										});
									}
								});
							});
						}
					}
				} else if (data.split(" ")[0] == "!p") {
					//Party
				} else {
					chat.clients.forEach(function each(client) {
						if (client.readyState === WebSocket.OPEN) {
							connection.query("SELECT citid from users where token = ?", [config.get("user." + client._socket.remoteAddress.replace(/::ffff:/g, '').replace(/\./g, '') + ".token")], function a(a, b) {
								uid = req.connection.remoteAddress.replace(/::ffff:/g, '').replace(/\./g, '');
								if (error) pdc(error, ip);
								if (b.length == 1) { try{client.send(json.stringify({ ok: true, display: config.get("user." + uid + ".un") + ">> " + data }));} catch (e) {pdc(e, ip);} }
							});
						}
					});
				}
			}

		});
	});

//Battlesocket, for processing battling sequences
	battle.on('connection', function connection(ws, req) {
		isconnected(req, ws);
		let ip = req.headers['x-forwarded-for'];
		let uid = ip.replace(/\./g, '');
		pppp = false;
		if(config.get("user."+uid+"battleid")) {try{ws.send(json.stringify({ok:true, code:2, bid: config.get("user."+uid+".battleid"), msg:"YOU_ARE_STILL_IN_A_FIGHT"}));} catch (e) {pdc(e, ip);}}
		else {
			r = uuid(7);
			connection.query("select uid from users where token = ?", [config.get("user."+uid+".token")], function(error,b) {
				pppp = b[0].uid;
			});
			console.log(config.get("user." + uid + ".token"));
			connection.query("SELECT * from monster where towns LIKE CONCAT('%', (select citid from users where token = ? ), '%') order by RAND() limit 1;", [config.get("user." + uid + ".token")], function(error,b) {
				if (error) pdc(error, ip);
				connection.query("INSERT INTO currentbattles (battleid, uid, mid, phealth, mhealth) VALUES (?, ?, ?, ?, ?)", [r, pppp, b[0].uid, 0, b[0].hp])
				try{ws.send(
					json.stringify({battleid: r, story:"STORY FOR BATTLEBOX",mobdata: {name: b[0].name, health: 5,ttlhealth: b[0].hp},playerdata: {name: "PLAYERNAME",health: 5,ttlhealth: 10,inventory: [],effects: [],canfight: true,istripped: false,isproceed: false,isabletoflee: true}}
				))} catch (e) {pdc(e, ip);}
			});
		}
	});

//Main server module. change anything here and I will kill you.
	httpServer.on('upgrade', function upgrade(request, socket, head) {
		const pathname = url.parse(request.url).pathname;

		if (pathname === '/main') {
			main.handleUpgrade(request, socket, head, function done(ws) {
				main.emit('connection', ws, request);
			});
		} else if (pathname === '/chat') {
			chat.handleUpgrade(request, socket, head, function done(ws) {
				chat.emit('connection', ws, request);
			});
		} else if (pathname === '/btl') {
			battle.handleUpgrade(request, socket, head, function done(ws) {
				battle.emit('connection', ws, request);
			});
		} else if (pathname === '/anon') {
			anon.handleUpgrade(request, socket, head, function done(ws) {
				anon.emit('connection', ws, request);
			});
		} else {
			socket.destroy();
		}
	});

 //Main port for server.
	httpServer.listen(2053);
