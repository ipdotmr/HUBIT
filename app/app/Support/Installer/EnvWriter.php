<?php

namespace App\Support\Installer;

use Illuminate\Filesystem\Filesystem;
use RuntimeException;

class EnvWriter
{
    public function __construct(
        protected Filesystem $filesystem,
        protected ?string $envPath = null,
    ) {
        $this->envPath ??= base_path('.env');
    }

    /**
     * @param array<string, scalar|null> $values
     */
    public function write(array $values): void
    {
        $envPath = $this->envPath;
        $directory = dirname($envPath);

        if (! $this->filesystem->exists($directory)) {
            $this->filesystem->makeDirectory($directory, 0755, true);
        }

        $existing = $this->filesystem->exists($envPath)
            ? $this->filesystem->get($envPath)
            : '';

        $updated = $this->mergeEnvironment($existing, $values);

        if ($existing !== '' && $existing !== $updated) {
            $backupName = $envPath . '.backup.' . now()->format('YmdHis');
            $this->filesystem->put($backupName, $existing);
        }

        $tmp = tempnam(sys_get_temp_dir(), 'env');
        if ($tmp === false) {
            throw new RuntimeException('Unable to write environment file.');
        }

        $this->filesystem->put($tmp, $updated);

        if ($this->filesystem->exists($envPath)) {
            $this->filesystem->delete($envPath);
        }

        $this->filesystem->move($tmp, $envPath);
    }

    protected function mergeEnvironment(string $existing, array $values): string
    {
        $lines = $existing !== '' ? preg_split('/\r\n|\n|\r/', $existing) : [];
        $lines = $lines === false ? [] : $lines;

        $handled = [];
        $result = [];

        foreach ($lines as $line) {
            if ($line === null) {
                continue;
            }

            if ($line === '' || str_starts_with(trim($line), '#')) {
                $result[] = $line;
                continue;
            }

            [$key, $value] = $this->splitKeyValue($line);
            if ($key !== null && array_key_exists($key, $values)) {
                $result[] = $this->formatLine($key, $values[$key]);
                $handled[$key] = true;
            } else {
                $result[] = $line;
            }
        }

        foreach ($values as $key => $value) {
            if (! array_key_exists($key, $handled)) {
                $result[] = $this->formatLine((string) $key, $value);
            }
        }

        return rtrim(implode(PHP_EOL, $result)) . PHP_EOL;
    }

    /**
     * @return array{0: ?string, 1: ?string}
     */
    protected function splitKeyValue(string $line): array
    {
        if (! str_contains($line, '=')) {
            return [null, null];
        }

        [$key, $value] = explode('=', $line, 2);

        return [trim($key), $value];
    }

    protected function formatLine(string $key, mixed $value): string
    {
        $scalar = $value;
        if (is_bool($value)) {
            $scalar = $value ? 'true' : 'false';
        }

        if ($scalar === null) {
            $scalar = '';
        }

        $stringValue = (string) $scalar;
        $needsQuotes = $stringValue === '' || preg_match('/\s|\#|=|"|\n/', $stringValue);

        if ($needsQuotes) {
            $stringValue = '"' . addcslashes($stringValue, "\n\r\"$") . '"';
        }

        return sprintf('%s=%s', $key, $stringValue);
    }
}
