<?php

namespace App\Support\Installer;

use Illuminate\Support\Str;
use Throwable;

class RequirementChecker
{
    /**
     * @return array<int, array<string, mixed>>
     */
    public function getChecks(): array
    {
        $checks = [];

        $checks[] = $this->makeCheck(
            key: 'php_version',
            label: 'PHP Version ≥ 8.2',
            status: version_compare(PHP_VERSION, '8.2.0', '>=') ? 'pass' : 'fail',
            message: version_compare(PHP_VERSION, '8.2.0', '>=')
                ? 'PHP version is compatible.'
                : 'Update PHP to 8.2 or newer.'
        );

        foreach ($this->requiredExtensions() as $extension) {
            $loaded = extension_loaded($extension);
            $checks[] = $this->makeCheck(
                key: 'ext_' . $extension,
                label: sprintf('Extension: %s', $extension),
                status: $loaded ? 'pass' : 'fail',
                message: $loaded ? 'Loaded' : 'Enable extension in php.ini',
            );
        }

        $gdOrImagick = extension_loaded('gd') || extension_loaded('imagick');
        $checks[] = $this->makeCheck(
            key: 'ext_gd_imagick',
            label: 'Extension: gd or imagick',
            status: $gdOrImagick ? 'pass' : 'fail',
            message: $gdOrImagick ? 'Loaded' : 'Install gd or imagick for image manipulation.',
        );

        $checks[] = $this->makeCheck(
            key: 'composer_autoload',
            label: 'Composer autoload (vendor/autoload.php)',
            status: file_exists(base_path('vendor/autoload.php')) ? 'pass' : 'fail',
            message: 'Run composer install if missing.'
        );

        foreach ($this->writablePaths() as $path => $label) {
            $absolute = base_path($path);
            $writable = is_writable($absolute);
            $checks[] = $this->makeCheck(
                key: 'writable_' . Str::slug($path),
                label: sprintf('Writable: %s', $label),
                status: $writable ? 'pass' : 'fail',
                message: $writable ? 'Writable' : 'Fix directory permissions',
            );
        }

        $checks[] = $this->makeCheck(
            key: 'storage_link',
            label: 'Storage symlink',
            status: $this->storageLinkStatus(),
            message: 'Run php artisan storage:link if missing.',
            required: false,
        );

        $checks[] = $this->makeCheck(
            key: 'database_connection',
            label: 'Database connectivity',
            status: 'pending',
            message: 'Pending configuration',
        );

        $checks[] = $this->makeCheck(
            key: 'redis',
            label: 'Redis extension & connection',
            status: $this->redisStatus(),
            message: 'Optional. Configure Redis for cache/queue.',
            required: false,
        );

        $checks[] = $this->makeCheck(
            key: 'node',
            label: 'Node.js runtime',
            status: $this->nodeStatus(),
            message: 'Optional. Needed for asset builds.',
            required: false,
        );

        $checks[] = $this->makeCheck(
            key: 'vite_manifest',
            label: 'Vite build manifest',
            status: file_exists(public_path('build/manifest.json')) || file_exists(public_path('mix-manifest.json')) ? 'pass' : 'warn',
            message: 'Ensure production assets are built (npm run build).',
            required: false,
        );

        $checks[] = $this->makeCheck(
            key: 'queue_horizon',
            label: 'Queue / Horizon configuration',
            status: 'warn',
            message: 'Optional. Configure queue workers post-install.',
            required: false,
        );

        $checks[] = $this->makeCheck(
            key: 'cron',
            label: 'Cron scheduling',
            status: 'warn',
            message: 'Add * * * * * php artisan schedule:run to cron.',
            required: false,
        );

        return $checks;
    }

    /**
     * @param array<int, array<string, mixed>> $checks
     */
    public function allRequiredPass(array $checks): bool
    {
        foreach ($checks as $check) {
            if (($check['required'] ?? true) && ! in_array($check['status'], ['pass', 'warn'], true)) {
                return false;
            }
        }

        return true;
    }

    /**
     * @return list<string>
     */
    protected function requiredExtensions(): array
    {
        return [
            'openssl',
            'pdo',
            'pdo_mysql',
            'mbstring',
            'tokenizer',
            'xml',
            'ctype',
            'json',
            'fileinfo',
            'bcmath',
            'curl',
            'zip',
            'intl',
        ];
    }

    /**
     * @return array<string, string>
     */
    protected function writablePaths(): array
    {
        return [
            'storage' => 'storage',
            'bootstrap/cache' => 'bootstrap/cache',
            'storage/framework' => 'storage/framework',
            'storage/logs' => 'storage/logs',
            'database' => 'database (for SQLite)',
        ];
    }

    protected function storageLinkStatus(): string
    {
        $link = public_path('storage');

        if (is_link($link) || file_exists($link)) {
            return 'pass';
        }

        return 'warn';
    }

    protected function redisStatus(): string
    {
        if (! extension_loaded('redis')) {
            return 'warn';
        }

        $default = config('database.redis.default');
        if (! $default) {
            return 'warn';
        }

        try {
            $client = app('redis')->connection();
            $client->ping();

            return 'pass';
        } catch (Throwable $e) {
            return 'warn';
        }
    }

    protected function nodeStatus(): string
    {
        try {
            @exec('node -v', $output, $status);

            return $status === 0 ? 'pass' : 'warn';
        } catch (Throwable $e) {
            return 'warn';
        }
    }

    /**
     * @param array<string, mixed> $attributes
     * @return array<string, mixed>
     */
    protected function makeCheck(string $key, string $label, string $status, string $message, bool $required = true): array
    {
        return [
            'key' => $key,
            'label' => $label,
            'status' => $status,
            'message' => $message,
            'required' => $required,
        ];
    }
}
