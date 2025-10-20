<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\File;
use Tests\TestCase;

class InstallerTest extends TestCase
{
    protected function tearDown(): void
    {
        $this->removeMarker();

        parent::tearDown();
    }

    public function test_install_routes_available_when_not_locked(): void
    {
        $this->removeMarker();

        $this->get('/install')->assertOk();
        $this->get('/install/checks')->assertOk();
    }

    public function test_install_routes_return_not_found_when_locked(): void
    {
        $marker = config('installer.marker_path');

        if ($marker) {
            File::ensureDirectoryExists(dirname($marker));
            File::put($marker, now()->toIso8601String());
        }

        $this->get('/install')->assertNotFound();
        $this->get('/install/checks')->assertNotFound();
    }

    protected function removeMarker(): void
    {
        $marker = config('installer.marker_path');

        if ($marker && File::exists($marker)) {
            File::delete($marker);
        }
    }
}
