<?php

namespace Tests\Feature\Install;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Mockery;
use Tests\TestCase;

class InstallerRunsStepsTest extends TestCase
{
    use RefreshDatabase;

    public function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_installer_runs_all_steps_and_creates_admin(): void
    {
        Mail::fake();
        config(['mail.default' => 'smtp']);

        $storagePath = base_path('storage/testing-run-' . Str::random(6));
        $originalStorage = app()->storagePath();
        app()->useStoragePath($storagePath);
        File::ensureDirectoryExists($storagePath . '/framework');

        session(['installer.admin' => [
            'name' => 'Admin User',
            'email' => 'owner@example.com',
            'password' => 'secret1234',
            'provided_password' => true,
        ]]);

        $artisan = Artisan::partialMock();
        $artisan->shouldReceive('output')->andReturn('')->byDefault();
        $artisan->shouldReceive('call')->once()->with('config:clear', [])->ordered()->andReturn(0);
        $artisan->shouldReceive('call')->once()->with('cache:clear', [])->ordered()->andReturn(0);
        $artisan->shouldReceive('call')->once()->with('route:clear', [])->ordered()->andReturn(0);
        $artisan->shouldReceive('call')->once()->with('view:clear', [])->ordered()->andReturn(0);
        $artisan->shouldReceive('call')->once()->with('migrate', ['--force' => true])->ordered()->andReturn(0);
        $artisan->shouldReceive('call')->once()->with('storage:link', [])->ordered()->andReturn(0);
        $artisan->shouldReceive('call')->once()->with('optimize', [])->ordered()->andReturn(0);
        $artisan->shouldReceive('call')->once()->with('queue:restart', [])->ordered()->andReturn(0);

        $this->postJson('/install/run', ['step' => 'clear_cache'])->assertOk();
        $this->postJson('/install/run', ['step' => 'migrate'])->assertOk();
        $this->postJson('/install/run', ['step' => 'storage_link'])->assertOk();
        $this->postJson('/install/run', ['step' => 'optimize'])->assertOk();
        $this->postJson('/install/run', ['step' => 'create_admin'])->assertOk();
        $this->postJson('/install/run', ['step' => 'test_mail'])
            ->assertOk()
            ->assertJson(fn ($json) => $json->where('success', true)->where('step', 'test_mail')->etc());
        $this->postJson('/install/run', ['step' => 'queue_restart'])->assertOk();
        $this->postJson('/install/run', ['step' => 'lock'])
            ->assertOk()
            ->assertJsonPath('finished', true);

        $this->assertDatabaseHas('users', [
            'email' => 'owner@example.com',
        ]);
        $this->assertFileExists($storagePath . '/framework/install.lock');

        File::deleteDirectory($storagePath);
        app()->useStoragePath($originalStorage);
    }
}
