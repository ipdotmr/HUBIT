<?php

namespace Tests;

use Illuminate\Contracts\Console\Kernel;

trait CreatesApplication
{
    public function createApplication()
    {
        $app = require __DIR__.'/../bootstrap/app.php';

        $app->make(Kernel::class)->bootstrap();

        if (env('APP_ENV') === 'testing') {
            config([
                'database.default' => 'sqlite',
                'database.connections.sqlite' => [
                    'driver' => 'sqlite',
                    'database' => base_path('database/database.sqlite'),
                    'prefix' => '',
                    'foreign_key_constraints' => true,
                ],
            ]);
            putenv('DATABASE_URL');
        }

        return $app;
    }
}
