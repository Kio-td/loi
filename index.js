const cfg1 = require('node-storage');
const http = require('http');
const WebSocket = require('ws');
const msql = require('mysql2');
const json = require('json5')
const url = require('url');
const cfg = new cfg1("./config/local.json");
const g = cfg.get('int.websock');
const fs = require('fs');
const ind = require('util');
const server = http.createServer();
const serve = new WebSocket.Server(g);
const chat = new WebSocket.Server(g);
const admin = new WebSocket.Server(g);
const battle = new WebSocket.Server(g);

var con = msql.createConnection(cfg.get("int.mysql"));
con.connect(function(err) {
	if (err) throw err;
	console.log("Connected to MySQL.");


	serve.on('connection', function connection(ws, req) {
		var ip = req.connection.remoteAddress;
		var ip = ip.replace(/::ffff:/g, '');
		var uid = ip.replace(/\./g, '');
		console.log(ip + " has connected.");
		if (cfg.get("user." + uid + ".ddm")) {
			console.log(ip + " has been recognized.");
			cfg.delete("user." + uid + ".ddm");
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
				console.log(cfg.get("user." + uid))
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
						var rows = 0
						con.query("SELECT username, bal from users where token = ?", [x["atoken"]], function asdf(a, b) {
							if (b.length == 1) {
								cfg.put("user." + uid + ".token", x["atoken"]);
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
		var ip = req.connection.remoteAddress;
		var uid = ip.replace(/\./g, '');

	});
	admin.on('connection', function connection(ws, req) {
		var ip = req.connection.remoteAddress;
		var uid = ip.replace(/\./g, '');

	});
	battle.on('connection', function connection(ws, req) {
		var ip = req.connection.remoteAddress;
		var uid = ip.replace(/\./g, '');

		//authencticate user
		if (true) {
			//Select a monster
			var mdb = [
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
		} else if (pathname === '/adm') {
			admin.handleUpgrade(request, socket, head, function done(ws) {
				admin.emit('connection', ws, request);
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