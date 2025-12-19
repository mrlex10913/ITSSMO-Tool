<?php

namespace Tests;

use Illuminate\Foundation\Application;

trait CreatesApplication
{
    public function createApplication(): Application
    {
        $app = require __DIR__.'/../bootstrap/app.php';

        // Bootstrap the application (sets up facades, config, providers)
        $app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

        // Ensure tests use SQLite in-memory regardless of cached production config
        $app['config']->set('database.default', 'sqlite');
        $app['config']->set('database.connections.sqlite.database', ':memory:');
        $app['config']->set('database.connections.sqlite.foreign_key_constraints', true);
        // Prevent any cached routes/config from forcing prod drivers during tests
        $app['config']->set('cache.default', 'array');
        $app['config']->set('session.driver', 'array');

        return $app;
    }
}
