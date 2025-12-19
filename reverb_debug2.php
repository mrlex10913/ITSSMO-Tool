<?php

echo "HELLO\n";
require __DIR__.'/vendor/autoload.php';
$app = require __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo 'env APP_ENV='.env('APP_ENV')."\n";

echo 'broadcasting.default='.config('broadcasting.default')."\n";
print_r(config('reverb'));

echo "DONE\n";
