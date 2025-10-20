<?php

namespace App\Http\Controllers\Install;

use Inertia\Inertia;
use Inertia\Response;

class InstallController
{
    public function __invoke(): Response
    {
        return Inertia::render('Install/Index', [
            'appName' => config('app.name', 'HUBIT'),
        ]);
    }
}
