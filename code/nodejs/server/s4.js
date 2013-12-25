module.paths.unshift('/usr/local/eyou/mail/opt/lib/node_modules/');

var io = require('socket.io').listen(3000, {'log level': 2});
io.sockets.on('connection', function(client) {
    client.emit('ready', 'ready');

    client.on('news', function(data) {
        console.info(data);
    });

    client.on('disconnect', function() {
        client.log.error('i have left.');
    });
});
