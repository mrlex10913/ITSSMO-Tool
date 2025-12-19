<?php

require __DIR__.'/vendor/autoload.php';
use React\EventLoop\Loop;

echo "before loop\n";
$loop = Loop::get();
$loop->addPeriodicTimer(1, function () {
    echo date('H:i:s')." tick\n";
});
$loop->run();

echo "after loop\n";
