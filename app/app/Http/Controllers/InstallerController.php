<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;
use RuntimeException;
use Throwable;

class InstallerController extends Controller
{
    public function welcome(): View
    {
        return view('install.welcome');
    }

    public function checks(): View
    {
        $config = config('installer');

        $phpCheck = [
            'label' => __('PHP :version or higher', ['version' => $config['min_php'] ?? '8.1']),
            'status' => version_compare(PHP_VERSION, $config['min_php'] ?? '8.1', '>='),
            'details' => 'v'.PHP_VERSION,
        ];

        $extensionChecks = collect($config['required_extensions'] ?? [])->map(function (string $extension) {
            return [
                'label' => $extension,
                'status' => extension_loaded($extension),
            ];
        });

        $permissionChecks = collect($config['required_permissions'] ?? [])->map(function (string $requirement, string $path) {
            $type = $requirement;
            $exists = file_exists($path);
            $status = false;
            $details = '';

            if (Str::startsWith($type, 'file')) {
                $target = $exists ? $path : dirname($path);
                $status = is_writable($target);
                $details = $exists
                    ? __('File is :state', ['state' => $status ? __('writable') : __('not writable')])
                    : __('Directory :dir is :state', ['dir' => dirname($path), 'state' => is_writable(dirname($path)) ? __('writable') : __('not writable')]);
            } elseif (Str::startsWith($type, 'dir')) {
                $directory = $exists ? $path : dirname($path);
                $status = is_dir($directory) && is_writable($directory);
                $details = $exists
                    ? __('Directory is :state', ['state' => $status ? __('writable') : __('not writable')])
                    : __('Parent directory :dir is :state', ['dir' => dirname($path), 'state' => is_writable(dirname($path)) ? __('writable') : __('not writable')]);
            }

            return [
                'label' => $path,
                'status' => $status,
                'details' => $details,
            ];
        });

        $databaseCheck = [
            'label' => __('Database connection'),
            'status' => false,
            'details' => __('Database credentials will be validated after saving your environment configuration.'),
        ];

        try {
            DB::connection()->getPdo();
            $databaseCheck['status'] = true;
            $databaseCheck['details'] = __('Connection established successfully.');
        } catch (Throwable $exception) {
            $databaseCheck['status'] = false;
            $databaseCheck['details'] = $exception->getMessage();
        }

        $allPassed = $phpCheck['status']
            && $extensionChecks->every(fn (array $check) => $check['status'])
            && $permissionChecks->every(fn (array $check) => $check['status']);

        return view('install.checks', [
            'phpCheck' => $phpCheck,
            'extensionChecks' => $extensionChecks,
            'permissionChecks' => $permissionChecks,
            'databaseCheck' => $databaseCheck,
            'allPassed' => $allPassed,
        ]);
    }

    public function envForm(Request $request): View
    {
        $defaults = [
            'app_name' => old('app_name', config('app.name', 'HUBIT')),
            'app_url' => old('app_url', config('app.url', url('/'))),
            'db_host' => old('db_host', config('database.connections.mysql.host', '127.0.0.1')),
            'db_port' => old('db_port', config('database.connections.mysql.port', '3306')),
            'db_database' => old('db_database', config('database.connections.mysql.database', 'hubit')),
            'db_username' => old('db_username', config('database.connections.mysql.username', 'root')),
            'db_password' => old('db_password', ''),
        ];

        return view('install.env', [
            'defaults' => $defaults,
            'hasExistingEnv' => file_exists(base_path('.env')),
        ]);
    }

    public function envSave(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'app_name' => ['required', 'string', 'max:255'],
            'app_url' => ['required', 'url'],
            'db_host' => ['required', 'string', 'max:255'],
            'db_port' => ['required', 'integer', 'min:1'],
            'db_database' => ['required', 'string', 'max:255'],
            'db_username' => ['required', 'string', 'max:255'],
            'db_password' => ['nullable', 'string'],
        ]);

        $envData = [
            'APP_NAME' => $validated['app_name'],
            'APP_ENV' => 'production',
            'APP_KEY' => env('APP_KEY'),
            'APP_DEBUG' => 'false',
            'APP_URL' => $validated['app_url'],
            'LOG_CHANNEL' => env('LOG_CHANNEL', 'stack'),
            'LOG_LEVEL' => env('LOG_LEVEL', 'info'),
            'DB_CONNECTION' => 'mysql',
            'DB_HOST' => $validated['db_host'],
            'DB_PORT' => (string) $validated['db_port'],
            'DB_DATABASE' => $validated['db_database'],
            'DB_USERNAME' => $validated['db_username'],
            'DB_PASSWORD' => $validated['db_password'] ?? '',
            'DATABASE_URL' => '',
            'QUEUE_CONNECTION' => env('QUEUE_CONNECTION', 'sync'),
            'MAIL_MAILER' => env('MAIL_MAILER', 'log'),
            'MAIL_FROM_ADDRESS' => env('MAIL_FROM_ADDRESS', 'noreply@example.com'),
            'MAIL_FROM_NAME' => env('MAIL_FROM_NAME', '${APP_NAME}'),
        ];

        $this->writeEnv($this->buildEnv($envData));

        Artisan::call('config:clear');
        Artisan::call('cache:clear');

        session()->put('installer.env_written', true);

        return redirect()->route('install.admin')
            ->with('status', __('Environment configuration saved successfully. Proceed to create your administrator account.'));
    }

    public function adminForm(Request $request): View
    {
        return view('install.admin', [
            'admin' => session('installer.admin', []),
            'envWritten' => session('installer.env_written', false) || file_exists(base_path('.env')),
        ]);
    }

    public function adminSave(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        session()->put('installer.admin', [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        return redirect()->route('install.admin')->with('status', __('Administrator details saved. You can now run the installation.'));
    }

    public function runInstall(Request $request): RedirectResponse
    {
        if (! file_exists(base_path('.env'))) {
            return redirect()->route('install.env')->withErrors([
                'env' => __('Please configure your environment before running the installer.'),
            ]);
        }

        $admin = session('installer.admin');

        if (! $admin) {
            return redirect()->route('install.admin')->withErrors([
                'admin' => __('Please provide administrator credentials before continuing.'),
            ]);
        }

        try {
            if (! config('app.key')) {
                Artisan::call('key:generate', ['--force' => true]);
            }

            Artisan::call('config:clear');
            Artisan::call('migrate', ['--force' => true]);

            $this->createAdministrator($admin);

            Artisan::call('optimize');

            $this->writeMarker();

            session()->forget(['installer.admin', 'installer.env_written']);

            return redirect()->route('install.done')->with('status', __('Installation completed successfully.'));
        } catch (Throwable $exception) {
            report($exception);

            return redirect()->route('install.admin')->withErrors([
                'install' => $exception->getMessage(),
            ]);
        }
    }

    public function done(): View|RedirectResponse
    {
        if (! file_exists(config('installer.marker_path'))) {
            return redirect()->route('install.welcome');
        }

        return view('install.done');
    }

    protected function buildEnv(array $values): string
    {
        return collect($values)
            ->map(function ($value, string $key) {
                $value = $value ?? '';
                $stringValue = (string) $value;

                if ($stringValue === '') {
                    return $key.'=';
                }

                if (preg_match('/\s|#|=|"|\'|\$/u', $stringValue)) {
                    $escaped = addcslashes($stringValue, "\\\"\n\r$");
                    return sprintf('%s="%s"', $key, $escaped);
                }

                return sprintf('%s=%s', $key, $stringValue);
            })
            ->implode(PHP_EOL)
            .PHP_EOL;
    }

    protected function writeEnv(string $contents): void
    {
        $path = base_path('.env');

        File::ensureDirectoryExists(dirname($path));
        File::put($path, $contents, true);
    }

    protected function writeMarker(): void
    {
        $marker = config('installer.marker_path');

        if (! $marker) {
            return;
        }

        File::ensureDirectoryExists(dirname($marker));
        File::put($marker, now()->toIso8601String(), true);
    }

    protected function createAdministrator(array $adminData): Model|Authenticatable
    {
        $modelClass = config('auth.providers.users.model') ?? \App\Models\User::class;

        if (! class_exists($modelClass)) {
            throw new RuntimeException('User model not found.');
        }

        /** @var Model|Authenticatable $model */
        $model = new $modelClass();

        $existing = $modelClass::query()->where('email', $adminData['email'])->first();

        if ($existing) {
            return $existing;
        }

        $model->forceFill([
            'name' => $adminData['name'],
            'email' => $adminData['email'],
            'password' => $adminData['password'],
        ]);

        if (Schema::hasColumn($model->getTable(), 'email_verified_at')) {
            $model->forceFill(['email_verified_at' => now()]);
        }

        $model->save();

        if (method_exists($model, 'assignRole')) {
            $model->assignRole('admin');
        } elseif (Schema::hasColumn($model->getTable(), 'is_admin')) {
            $model->forceFill(['is_admin' => true])->save();
        }

        return $model;
    }
}
