<?php

require __DIR__.'/vendor/autoload.php';
use React\EventLoop\Loop;
use React\Socket\SocketServer;

$loop = Loop::get();
$server = new SocketServer('0.0.0.0:6001', [], $loop);
$server->on('connection', function ($conn) {
    echo "client connected\n";
});

echo "listening on 0.0.0.0:6001\n";
$loop->run();
