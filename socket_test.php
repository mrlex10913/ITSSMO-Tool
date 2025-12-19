<?php

require __DIR__.'/vendor/autoload.php';
use React\EventLoop\Loop;
use React\Socket\SocketServer;

$loop = Loop::get();
$server = new SocketServer('127.0.0.1:7000', [], $loop);
$server->on('connection', function ($conn) {
    echo "client connected\n";
    $conn->on('data', function ($data) use ($conn) {
        echo 'recv: '.$data."\n";
        $conn->write("ok\n");
    });
});

echo "listening on 127.0.0.1:7000\n";
$loop->run();
