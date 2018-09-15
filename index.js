const cfg1 = require('node-storage');
const http = require('https');
const mail = require('@sendgrid/mail');
const pass = require('node-php-password');
const WebSocket = require('ws');
const msql = require('mysql2');
const json = require('json5')
const url = require('url');
const cfg = new cfg1("./config/local.json"); //Configuration for Server, and for users
const mcg = new cfg1("./config/hvw.json"); //HVW - High-Velocity Writing - This function will most likely be written to A LOT. It's important to make sure that the API will always be ready.
const g = cfg.get('int.websock');
const uid = require('shortid');
const fs = require('fs');
const server = http.createServer({
	cert: fs.readFileSync('config/cert.pem'),
	key: fs.readFileSync('config/key.pem')
});
const serve = new WebSocket.Server(g);
const chat = new WebSocket.Server(g);
const battle = new WebSocket.Server(g);
const anon = new WebSocket.Server(g);
const black = ["ikaros", "admin", "console", "sysadmin", "owner", "dev", "developer", "support", "superuser", "root", "system", "bot", "npc"];

mail.setApiKey(cfg.get("int.sg"));

function sendemail(to, template, data) {
	msg = {
		to: to,
		from: "Legend of Ikaros <Noreply@loi.nayami.party>",
		templateId: template,
		dynamic_template_data: data
	}
	mail.send(msg);
}

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

	anon.on('connection', function(ws, req) {
			ws.send(json.stringify({ok:true, code:3, msg:"HI_ANON"}));
			ws.on('message', function(data) {
				try {
					d = json.parse(data);
				} catch (e) {
					ws.send(json.stringify({ok:false, code:-3, msg:"NOT_JSON5"}));
				}
				if (d["cmd"] == undefined) {
					ws.send(json.stringify({ok:false, code:-1, msg:"NO_CMD"}));
					return;
				} else if (d["cmd"] == "species") {
					con.query("select * from spec", function(a,b) {
						if(a) throw a;
						ws.send(json.stringify({ok:true, code:4, data:b}));
					});
				} else if (d["cmd"] == "auth") {
					if(d["data"] == undefined) {
						ws.send(json.stringify({ok:false, code:-3, msg:"NO_DATA_FOUND"}));
					} else {
						data = d["data"]
						//un, pw
						data.un = data.un.toLowerCase();
						data.pw = pass.hash(data.pw);
						con.query("select ce, password, token from users where username = ?", [data.un], function(a,b) {
							if (a) throw a;
							s = b[0];
							if(s.ce !== "0") {ws.send(json.stringify({ok:false, code:-4, msg:"CONF_EMAIL"}));}
							else if(pass.verify(data.pw, s.password) == false) {
								ws.send(json.stringify({ok:false, code:-4, msg:"INC_PASS"}));
							} else {
								ws.send(json.stringify({ok:true, code:4, data:s.token}));
							}
						})
					}
				} else if (d["cmd"] == "cun") {
					if(d["data"] == undefined) {
						ws.send(json.stringify({ok:false, code:-3, msg:"NO_DATA_FOUND"}));
					} else if (black.includes(d["data"].toLowerCase())) {
						ws.send(json.stringify({ok:false, code:-4, data:"BL"}))
					} else {
					con.query("select username from users where username = ?", [d["data"].toLowerCase()], function (a, b) {
						if (a) throw a;
						if (b.length > 0) {ws.send(json.stringify({ok:true, code:4, data:"F"}));}
						else {ws.send(json.stringify({ok:true, code:4, data:"NF"}));}
					});
				}
			} else if (d["cmd"] == "cem") {
				if(d["data"] == undefined) {
					ws.send(json.stringify({ok:false, code:-3, msg:"NO_DATA_FOUND"}));
				} else {
					con.query("select username from users where email = ?", [d["data"]], function (a, b) {
						if (a) throw a;
						if (b.length > 0) {ws.send(json.stringify({ok:true, code:4, data:false}));}
						else {ws.send(json.stringify({ok:true, code:4, data:true}));}
					});
				}
			} else if (d["cmd"] == "create") {
				if(d["data"] == undefined) {
					ws.send(json.stringify({ok:false, code:-3, msg:"NO_DATA_FOUND"}));
				} else {
					n = d.data;
					//un, em, pw, sp
					n.un = n.un.toLowerCase();
					n.em = n.em.toLowerCase();
					con.query("select username from users where username = ? or email = ?", [n.un, n.em], function (a, b) {
						if(b.length > 0) {ws.send(json.stringify({ok: false, code:6, msg: "ACCT_EXISTS"}))}
						else if (black.includes(n.un.toLowerCase()))  {ws.send(json.stringify({ok: false, code:6, msg: "ACCT_BLACKLIST"}))}
						else {
							token = uid.generate() + uid.generate() + uid.generate() + uid.generate() + uid.generate() + uid.generate();
							ce = uid.generate() + uid.generate() + uid.generate() + uid.generate() + uid.generate() + uid.generate();
							con.query("INSERT INTO `users`(`username`, `password`, `email`, `token`, `ce`, `spid`) VALUES (?,?,?,?,?,?);", [n.un, pass.hash(n.pw),n.em, token, ce, n.sp], function (a) {
								if (a) throw a;
								sendemail(n.em, "d-d506a7695d094d6196dac374b1e00630", {username: n.un, url: "https://loi.nayami.party/game/login-?confirm=" + ce + "&username=" + n.un});
								ws.send(json.stringify({ok:true, code:4, msg:"CHECK_EMAIL"}));
							});
						}
					});
				}
			}
			});
		});

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
			let ip = req.connection.remoteAddress.replace(/::ffff:/g, '');
			let uid = ip.replace(/\./g, '');
			ds = 0
			data = data.split(/\r?\n|\r/g)[0].replace(/\</g, '&lt;').replace(/\>/g, '&gt;').trim();
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
						con.query("select uid from users where token = ?", [cfg.get("user." + uid + ".token")], function(a, b) {
							con.query("select token from users where uid in (select ut from friends where uf = ?) or uid in (select uf from friends where ut = ?) and ? != uid", [b[0].uid, b[0].uid, b[0].uid], function(c, d) {
								if (c) throw c;
								d.forEach(function(h) {
									chat.clients.forEach(function each(client) {
										if (client.readyState === WebSocket.OPEN) {
											if (cfg.get("user." + client._socket.remoteAddress.replace(/::ffff:/g, '').replace(/\./g, '') + ".token") == h.token) {
												con.query("SELECT citid from users where token = ?", [cfg.get("user." + client._socket.remoteAddress.replace(/::ffff:/g, '').replace(/\./g, '') + ".token")], function a(e, f) {
													uid = req.connection.remoteAddress.replace(/::ffff:/g, '').replace(/\./g, '');
													if (e) throw e;
													if (f.length == 1) { client.send(json.stringify({ ok: true, display: cfg.get("user." + uid + ".un") + ">> " + data.replace("!f all ", ""), color: "pink" })); }
												});
											}
										}
									});
								});
								ws.send(json.stringify({ ok: true, display: cfg.get("user." + uid + ".un") + ">> " + data.replace("!f all ", ""), color: "pink" }));
							});
						});
					} else {
						fun = data.split(" ")[1].toLowerCase();
						if (fun == cfg.get("user." + uid + ".un")) { ws.send(json.stringify({ ok: false, display: "You cannot send a message to yourself." })) } else {

							con.query("select uid from users where token = ?", [cfg.get("user." + uid + ".token")], function(f, g) {
								con.query("select token from users where username = ? and uid in ( select ut from friends where uf = ? ) or uid in ( select uf from friends where ut = ? )", [fun, g[0].uid, g[0].uid], function(a, b) {
									if (b.length == 0) { ws.send(json.stringify({ ok: false, display: fun + " is not a friend of yours. Are you sure you typed their name correctly?" })) } else {
										chat.clients.forEach(function each(client) {
											if (client.readyState === WebSocket.OPEN) {
												con.query("SELECT citid from users where token = ?", [cfg.get("user." + client._socket.remoteAddress.replace(/::ffff:/g, '').replace(/\./g, '') + ".token")], function a(a, e) {
													uid = req.connection.remoteAddress.replace(/::ffff:/g, '').replace(/\./g, '');
													if (a) throw a;
													if (b.length == 1) {
														client.send(json.stringify({ ok: true, display: cfg.get("user." + uid + ".un") + ">> " + data.replace("!f " + fun + " ", ""), color: "pink" }));
														ws.send(json.stringify({ ok: true, display: cfg.get("user." + uid + ".un") + ">> " + data.replace("!f " + fun + " ", ""), color: "pink" }));
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
							con.query("SELECT citid from users where token = ?", [cfg.get("user." + client._socket.remoteAddress.replace(/::ffff:/g, '').replace(/\./g, '') + ".token")], function a(a, b) {
								uid = req.connection.remoteAddress.replace(/::ffff:/g, '').replace(/\./g, '');
								if (a) throw a;
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
		if(cnf.get("user."+uid+"battleid")) {ws.send(json.stringify({ok:true, code:2, bid: cnf.get("user."+uid+"battleid"), msg:"YOU_ARE_STILL_IN_A_FIGHT"}));}
		else {
			r = Math.random().toString(36).substring(7);
		}
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
		} else if (pathname === '/anon') {
			anon.handleUpgrade(request, socket, head, function done(ws) {
				anon.emit('connection', ws, request);
			});
		} else {
			socket.destroy();
		}
	});

	server.listen(2053);
});