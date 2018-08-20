const cfg = require('config');
const http = require('http');
const WebSocket = require('ws');
const msql = require('mysql');
const json = require('json5')
const url = require('url');

const server = http.createServer();
const serve = new WebSocket.Server({ noServer: true });
const chat = new WebSocket.Server({ noServer: true });
const admin = new WebSocket.Server({ noServer: true });
const battle = new WebSocket.Server({ noServer: true });

serve.on('connection', function connection(ws) {
  if(true) {
    ws.send('{code:1}');
    ws.on('message', function incoming(data) {
      console.log(data);
        x = json.parse(data);
    });
  } else {
    ws.send('{"code":2}');
  }
});
chat.on('connection', function connection(ws) {

});
admin.on('connection', function connection(ws) {

});
battle.on('connection', function connection(ws) {

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
