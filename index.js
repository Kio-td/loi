const cfg1 = require('node-storage');
const http = require('https');
const WebSocket = require('ws');
const msql = require('mysql2');
const json = require('json5')
const url = require('url');
const cfg = new cfg1("./config/local.json");
const g = cfg.get('int.websock');
const fs = require('fs');
const server = http.createServer({
	cert: fs.readFileSync('config/cert.pem'),
	key: fs.readFileSync('config/key.pem')
});
const serve = new WebSocket.Server(g);
const chat = new WebSocket.Server(g);
const battle = new WebSocket.Server(g);

function isconnected(req, ws) {
	let ip = req.connection.remoteAddress.replace(/::ffff:/g, '');
	let uid = ip.replace(/\./g, '');
	if (cfg.get("user." + uid + ".token") == undefined) {
		ws.close(1013);
	}
}

let con = msql.createConnection(cfg.get("int.mysql"));

con.connect(function(err) {
	if (err) throw err;
	console.log("Connected to MySQL, and server is running.");


	serve.on('connection', function connection(ws, req) {
		let ip = req.connection.remoteAddress.replace(/::ffff:/g, '');
		let uid = ip.replace(/\./g, '');
		console.log(ip + " has connected.");
		if (cfg.get("user." + uid + ".ddm")) {
			console.log(ip + " has been recognized.");
			cfg.remove("user." + uid + ".ddm");
		}
		ws.on('close', function dc(code, reason) {
			console.log(ip + " has disconnected.");
			if (!cfg.get("user." + uid + ".ddm")) {
				cfg.remove("user." + uid);
			}
		});
		if (cfg.get("user." + uid) == undefined) {
			ws.send('{code:1}');
			ws.on('message', function incoming(data) {
				//user is authenticated
				if (typeof cfg.get("user." + uid) !== "undefined") {
					try {
						x = json.parse(data);
					} catch (e) {
						ws.send(json.stringify({
							ok: false,
							msg: "not_JSON5",
							code: -2
						}));
						return;
					}
					if (x["cmd"] == undefined) {
						ws.send(json.stringify({ ok: false, msg: "cmd_not_found", code: -1 }));
						return;
					} else {
						if (x["cmd"] == "ddm") {
							cfg.put("user." + uid + ".ddm", true);
							ws.send(json.stringify({
								ok: true,
								msg: "SO_REMEMBER_ME_AND_I_WILL_REMEMBER_YOU",
								code: 2152
							}));
							return;
						} else if (x["cmd"] == "charge") {

						}
					}
				} else {
					try { x = json.parse(data); } catch (e) { ws.close(1013); return; }
					if (x["atoken"] == undefined) ws.close(1013)
					else {
						let rows = 0
						con.query("SELECT username, bal from users where token = ?", [x["atoken"]], function asdf(a, b) {
							if (b.length == 1) {
								cfg.put("user." + uid + ".token", x["atoken"]);
								cfg.put("user." + uid + ".un", b[0].username);
								console.log(ip + " identified as " + b[0].username + ".");
								ws.send(json.stringify({ ok: true, msg: "I_THOUGHT_I_REMEMBERED_YOU_OWO", code: 5 }));
							} else ws.close(1013);
						});
					}
				}
			});
		} else {
			ws.send('{"code":2}');
		}
	});

	chat.on('connection', function connection(ws, req) {
		isconnected(req, ws);
		ws.on('message', function msg(data) {
			ds = 0
			data = data.split(/\r?\n|\r/g)[0].replace(/\</g, '&lt;').replace(/\>/g, '&gt;');
			if (data.length >= 80) {
				ws.send(json.stringify({ ok: false, display: "*Your voice falls on deaf ears. (Too many characters.)", color: "red" }));
				ds = 1
			}
			if (ds == 0) {
				if (data.split(" ")[0] == "!s") {
					//Use shout
					ws.send(json.stringify({ ok: false, display: "*Shouts are not setup yet.", color: "red" }));
				} else if (data.split(" ")[0] == "!g") {
					//Guilds
				} else if (data.split(" ")[0] == "!f") {
					if (data.split(" ")[1] == "all") {
						con.query("select uid from users where token = ?", [cfg.get("user." + uid + ".token")]);
						con.query("select * from users where `uid` in (SELECT ut from friends) or `uid` in (SELECT uf from friends) and USERID not in (select uf from friends)", function(a, b) {

						});
					} else {
						//Check for online friend and check if online.
					}
				} else if (data.split(" ")[0] == "!p") {
					//Party
				} else {
					chat.clients.forEach(function each(client) {
						if (client.readyState === WebSocket.OPEN) {
							con.query("SELECT citid from users where token = ?", [cfg.get("user." + client._socket.remoteAddress.replace(/::ffff:/g, '').replace(/\./g, '') + ".token")], function a(a, b) {
								uid = req.connection.remoteAddress.replace(/::ffff:/g, '').replace(/\./g, '');
								if (b.length == 1) { client.send(json.stringify({ ok: true, display: cfg.get("user." + uid + ".un") + ">> " + data })); }
							});
						}
					});
				}
			}

		});
	});

	battle.on('connection', function connection(ws, req) {
		isconnected(req, ws);
		//authencticate user
		if (true) {
			//Select a monster
			let mdb = [
				1, //Wolf
				2, //Hatter
				3 //Xavier
			]

		} else ws.close(1013);

	});

	server.on('upgrade', function upgrade(request, socket, head) {
		const pathname = url.parse(request.url).pathname;

		if (pathname === '/main') {
			serve.handleUpgrade(request, socket, head, function done(ws) {
				serve.emit('connection', ws, request);
			});
		} else if (pathname === '/ccon') {
			chat.handleUpgrade(request, socket, head, function done(ws) {
				chat.emit('connection', ws, request);
			});
		} else if (pathname === '/btl') {
			battle.handleUpgrade(request, socket, head, function done(ws) {
				battle.emit('connection', ws, request);
			});
		} else {
			socket.destroy();
		}
	});

	server.listen(8096);
});