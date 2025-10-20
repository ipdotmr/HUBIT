<?php

namespace Tests\Feature\Install;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Tests\TestCase;

class InstallerLockTest extends TestCase
{
    public function test_installer_returns_not_found_when_locked(): void
    {
        $storagePath = base_path('storage/testing-lock-' . Str::random(6));
        $originalStorage = app()->storagePath();
        app()->useStoragePath($storagePath);
        File::ensureDirectoryExists($storagePath . '/framework');
        File::put($storagePath . '/framework/install.lock', now()->toDateTimeString());

        $response = $this->get('/install');
        $response->assertNotFound();

        File::deleteDirectory($storagePath);
        app()->useStoragePath($originalStorage);
    }
}
