<?php

$app = require __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

function out($k, $v)
{
    echo $k.'='.(is_array($v) ? json_encode($v) : var_export($v, true)).PHP_EOL;
}

out('broadcasting.default', config('broadcasting.default'));
out('reverb.default', config('reverb.default'));
out('server.host', config('reverb.servers.reverb.host'));
out('server.port', config('reverb.servers.reverb.port'));
out('server.path', config('reverb.servers.reverb.path'));
out('server.hostname', config('reverb.servers.reverb.hostname'));
out('server.options', config('reverb.servers.reverb.options'));
out('server.max_request_size', config('reverb.servers.reverb.max_request_size'));
out('server.scaling.enabled', config('reverb.servers.reverb.scaling.enabled'));
out('server.scaling.server', config('reverb.servers.reverb.scaling.server'));
out('server.pulse_ingest_interval', config('reverb.servers.reverb.pulse_ingest_interval'));
out('server.telescope_ingest_interval', config('reverb.servers.reverb.telescope_ingest_interval'));

$appKey = config('reverb.apps.apps.0.key');
$appSecret = config('reverb.apps.apps.0.secret');
$appId = config('reverb.apps.apps.0.app_id');
$options = config('reverb.apps.apps.0.options');

out('app.key', $appKey);
out('app.secret', $appSecret);
out('app.id', $appId);
out('client.options', $options);
