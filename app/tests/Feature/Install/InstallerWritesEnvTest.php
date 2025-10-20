<?php

namespace Tests\Feature\Install;

use App\Support\Installer\EnvWriter;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;
use Mockery;
use Tests\TestCase;

class InstallerWritesEnvTest extends TestCase
{
    public function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_installer_writes_env_file_and_generates_key(): void
    {
        $tempDir = base_path('storage/testing-env-' . Str::random(6));
        (new Filesystem())->ensureDirectoryExists($tempDir);
        $envPath = $tempDir . '/.env';

        $filesystem = new Filesystem();
        $this->app->instance(EnvWriter::class, new EnvWriter($filesystem, $envPath));

        Artisan::shouldReceive('call')
            ->once()
            ->with('key:generate', ['--force' => true])
            ->andReturn(0);

        $payload = [
            'app_name' => 'Test App',
            'app_url' => 'https://example.com',
            'app_locale' => 'en',
            'app_fallback_locale' => 'en',
            'app_timezone' => 'UTC',
            'app_env' => 'production',
            'app_debug' => false,
            'db_driver' => 'mysql',
            'db_host' => '127.0.0.1',
            'db_port' => 3306,
            'db_database' => 'hubit',
            'db_username' => 'hubit',
            'db_password' => 'secret',
            'cache_driver' => 'file',
            'queue_connection' => 'sync',
            'mail_mailer' => 'log',
            'mail_host' => null,
            'mail_port' => null,
            'mail_username' => null,
            'mail_password' => null,
            'mail_encryption' => null,
            'mail_from_address' => null,
            'mail_from_name' => null,
            'admin_name' => 'Admin',
            'admin_email' => 'admin@example.com',
            'admin_password' => 'password123',
        ];

        $response = $this->postJson('/install/write-env', $payload);

        $response->assertOk();
        $this->assertFileExists($envPath);
        $contents = file_get_contents($envPath);
        $this->assertStringContainsString('APP_NAME="Test App"', $contents);
        $this->assertStringContainsString('DB_DATABASE=hubit', $contents);

        (new Filesystem())->deleteDirectory($tempDir);
    }
}
