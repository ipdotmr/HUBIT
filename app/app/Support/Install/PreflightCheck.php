<?php

namespace App\Support\Install;

use Illuminate\Support\Collection;

class PreflightCheck
{
    /**
     * Run the HUBIT installer preflight checks.
     */
    public static function run(): array
    {
        $checks = static::checks();

        return [
            'ok' => $checks->every(
                fn (array $check) => $check['status'] || ($check['severity'] ?? 'required') !== 'required'
            ),
            'checks' => $checks->values()->all(),
        ];
    }

    /**
     * @return Collection<int, array{key: string, label: string, status: bool, message: string, severity?: string}>
     */
    public static function checks(): Collection
    {
        $checks = collect();

        $checks->push(static::makeCheck(
            key: 'php.version',
            label: 'PHP version (>= 8.2)',
            status: version_compare(PHP_VERSION, '8.2.0', '>='),
            message: 'Detected PHP ' . PHP_VERSION,
        ));

        $extensions = collect(config('install.extensions.required', []))
            ->map(fn (string $label) => [
                'label' => $label,
                'severity' => 'required',
            ])
            ->merge(
                collect(config('install.extensions.optional', []))
                    ->map(fn (string $label) => [
                        'label' => $label,
                        'severity' => 'optional',
                    ])
            );

        foreach ($extensions as $extension => $options) {
            $loaded = extension_loaded($extension);
            $label = $options['label'];
            $severity = $options['severity'] ?? 'required';

            $checks->push(static::makeCheck(
                key: "php.extension.{$extension}",
                label: "PHP extension: {$label}",
                status: $loaded,
                message: $loaded
                    ? "{$label} extension is loaded."
                    : "Enable the {$label} extension in PHP.",
                severity: $severity,
            ));
        }

        $paths = collect(config('install.paths', []))->map(function ($definition) {
            if (is_string($definition)) {
                return [
                    'label' => "Writable path: {$definition}",
                    'path' => $definition,
                ];
            }

            return [
                'label' => $definition['label'] ?? "Writable path: {$definition['path']}",
                'path' => $definition['path'],
            ];
        });

        foreach ($paths as $name => $definition) {
            $path = $definition['path'];
            $label = $definition['label'];
            $isWritable = is_writable($path);

            $checks->push(static::makeCheck(
                key: "filesystem.{$name}",
                label: $label,
                status: $isWritable,
                message: $isWritable
                    ? 'Directory is writable.'
                    : 'Grant the web server write access to this directory.',
            ));
        }

        if (file_exists(base_path('.env'))) {
            $checks->push(static::makeCheck(
                key: 'environment.file',
                label: '.env configuration file exists',
                status: true,
                message: '.env file detected.',
                severity: 'optional',
            ));
        } else {
            $checks->push(static::makeCheck(
                key: 'environment.file',
                label: '.env configuration file exists',
                status: false,
                message: 'The installer will create a .env file if one is missing.',
                severity: 'optional',
            ));
        }

        return $checks;
    }

    /**
     * @return array{key: string, label: string, status: bool, message: string, severity?: string}
     */
    protected static function makeCheck(
        string $key,
        string $label,
        bool $status,
        string $message,
        string $severity = 'required'
    ): array {
        return [
            'key' => $key,
            'label' => $label,
            'status' => $status,
            'message' => $message,
            'severity' => $severity,
        ];
    }
}
