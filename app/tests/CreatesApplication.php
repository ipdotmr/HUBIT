<?php

namespace Tests;

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

trait CreatesApplication
{
    /**
     * Create the application instance.
     */
    public function createApplication(): Application
    {
        $app = require __DIR__.'/../bootstrap/app.php';

        $app->make(Kernel::class)->bootstrap();

        if ($app->environment('testing')) {
            $this->configureSqliteDatabase($app);
        }

        return $app;
    }

    protected function configureSqliteDatabase(Application $app): void
    {
        $database = env('DB_DATABASE', database_path('database.sqlite'));

        if (! Str::startsWith($database, 'memory:')) {
            $absolutePath = Str::startsWith($database, DIRECTORY_SEPARATOR)
                ? $database
                : $app->basePath($database);

            File::ensureDirectoryExists(dirname($absolutePath));

            if (! File::exists($absolutePath)) {
                File::put($absolutePath, '');
            }

            $database = $absolutePath;
        }

        Config::set('database.default', 'sqlite');
        Config::set('database.connections.sqlite.database', $database);
        Config::set('database.connections.sqlite.foreign_key_constraints', true);
    }
}
