<?php

namespace App\Http\Controllers\Install;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Support\Installer\EnvWriter;
use App\Support\Installer\RequirementChecker;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Throwable;

class InstallerController extends Controller
{
    public function __construct(
        protected RequirementChecker $checker,
        protected EnvWriter $envWriter,
    ) {
    }

    public function index(Request $request)
    {
        $checks = $this->checker->getChecks();

        return view('install.index', [
            'checks' => $checks,
            'canContinue' => $this->checker->allRequiredPass($checks),
        ]);
    }

    public function checkDb(Request $request): JsonResponse
    {
        $data = $this->validateDatabase($request);

        $connection = 'installer-temp-' . Str::random(6);
        config(["database.connections.$connection" => [
            'driver' => $data['driver'],
            'host' => $data['host'],
            'port' => $data['port'],
            'database' => $data['database'],
            'username' => $data['username'],
            'password' => $data['password'],
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
        ]]);

        try {
            DB::connection($connection)->getPdo();
        } catch (Throwable $e) {
            DB::purge($connection);

            return response()->json([
                'ok' => false,
                'message' => $e->getMessage(),
            ], 422);
        }

        DB::purge($connection);

        return response()->json([
            'ok' => true,
            'message' => 'Database connection successful.',
        ]);
    }

    public function writeEnv(Request $request): JsonResponse
    {
        $data = $this->validateConfiguration($request);

        $envValues = $this->mapToEnv($data);

        $this->envWriter->write($envValues);

        if (empty(env('APP_KEY')) && empty($envValues['APP_KEY'] ?? null)) {
            Artisan::call('key:generate', ['--force' => true]);
        }

        $request->session()->put('installer.admin', [
            'name' => $data['admin_name'],
            'email' => $data['admin_email'],
            'password' => $data['admin_password'] ?? null,
            'provided_password' => filled($data['admin_password'] ?? null),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Environment configuration saved.',
            'admin' => [
                'email' => $data['admin_email'],
                'password_supplied' => filled($data['admin_password'] ?? null),
            ],
        ]);
    }

    public function run(Request $request): JsonResponse
    {
        $step = $request->string('step')->toString();

        $steps = [
            'clear_cache' => fn () => $this->runClearCaches(),
            'migrate' => fn () => $this->runMigrations(),
            'storage_link' => fn () => $this->runStorageLink(),
            'optimize' => fn () => $this->runOptimize(),
            'create_admin' => fn () => $this->runCreateAdmin($request),
            'test_mail' => fn () => $this->runTestMail($request),
            'queue_restart' => fn () => $this->runQueues(),
            'lock' => fn () => $this->runLock($request),
        ];

        if (! array_key_exists($step, $steps)) {
            return response()->json([
                'success' => false,
                'message' => 'Unknown installation step.',
            ], 422);
        }

        try {
            $result = $steps[$step]();
        } catch (Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => $this->limitLines($e->getMessage()),
            ], 422);
        }

        return response()->json(array_merge([
            'success' => true,
            'step' => $step,
        ], $result));
    }

    protected function runClearCaches(): array
    {
        $output = [];
        $output[] = $this->callArtisan('config:clear');
        $output[] = $this->callArtisan('cache:clear');
        $output[] = $this->callArtisan('route:clear');
        $output[] = $this->callArtisan('view:clear');

        return ['output' => $this->normalizeOutput($output)];
    }

    protected function runMigrations(): array
    {
        $output = $this->callArtisan('migrate', ['--force' => true]);

        return ['output' => $output !== '' ? $output : 'Migrations executed.'];
    }

    protected function runStorageLink(): array
    {
        if (is_link(public_path('storage')) || file_exists(public_path('storage'))) {
            return ['output' => 'Storage link already exists.'];
        }

        $output = $this->callArtisan('storage:link');

        return ['output' => $output !== '' ? $output : 'Storage link created.'];
    }

    protected function runOptimize(): array
    {
        $output = $this->callArtisan('optimize');

        return ['output' => $output !== '' ? $output : 'Application optimized.'];
    }

    protected function runCreateAdmin(Request $request): array
    {
        if (User::query()->exists()) {
            return ['output' => 'Admin user already exists. Skipping.'];
        }

        $admin = $request->session()->get('installer.admin', []);

        $defaultHost = parse_url(config('app.url', 'http://localhost'), PHP_URL_HOST) ?: 'localhost';
        $name = $admin['name'] ?? 'Administrator';
        $email = $admin['email'] ?? 'admin@' . $defaultHost;
        $password = $admin['password'] ?? null;
        $generated = false;

        if (empty($password)) {
            $password = Str::random(16);
            $generated = true;
        }

        User::forceCreate([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($password),
        ]);

        if ($generated) {
            $request->session()->put('installer.admin.generated_password', $password);
        } else {
            $request->session()->forget('installer.admin.generated_password');
        }

        return [
            'output' => 'Admin user created: ' . $email,
            'generatedPassword' => $generated ? $password : null,
        ];
    }

    protected function runTestMail(Request $request): array
    {
        $mailer = config('mail.default');

        if (empty($mailer) || in_array($mailer, ['log', 'array', 'failover'], true)) {
            return ['output' => 'Mail configuration set to log/array. Skipping send.'];
        }

        $admin = $request->session()->get('installer.admin', []);
        $recipient = $admin['email'] ?? null;

        if (! $recipient) {
            return ['output' => 'No admin email available for test mail.'];
        }

        try {
            Mail::raw('HUBIT installer test mail sent at ' . now()->toDateTimeString(), function ($message) use ($recipient) {
                $message->to($recipient)->subject('HUBIT Installer Test Mail');
            });
        } catch (Throwable $e) {
            return [
                'output' => 'Mail attempt failed: ' . $this->limitLines($e->getMessage()),
            ];
        }

        return ['output' => 'Test mail dispatched to ' . $recipient . '.'];
    }

    protected function runQueues(): array
    {
        $output = [];

        $output[] = $this->callArtisan('queue:restart');

        if (class_exists(\Laravel\Horizon\Horizon::class)) {
            $output[] = $this->callArtisan('horizon:terminate');
        }

        return ['output' => $this->normalizeOutput($output) ?: 'Queue workers restarted.'];
    }

    protected function runLock(Request $request): array
    {
        $lockPath = storage_path('framework/install.lock');
        File::ensureDirectoryExists(dirname($lockPath));
        File::put($lockPath, now()->toDateTimeString());

        $generated = $request->session()->pull('installer.admin.generated_password');
        $request->session()->forget('installer.admin');

        return [
            'output' => 'Installation locked.',
            'generatedPassword' => $generated,
            'finished' => true,
        ];
    }

    protected function callArtisan(string $command, array $parameters = []): string
    {
        Artisan::call($command, $parameters);

        return trim(Artisan::output());
    }

    protected function normalizeOutput(array $output): string
    {
        $filtered = array_values(array_filter($output, fn ($line) => $line !== ''));

        return implode(PHP_EOL, $filtered);
    }

    protected function limitLines(string $message): string
    {
        $lines = preg_split('/\r\n|\n|\r/', $message) ?: [];

        return implode(PHP_EOL, array_slice($lines, 0, 15));
    }

    protected function validateDatabase(Request $request): array
    {
        $rules = [
            'driver' => ['required', 'string'],
            'host' => ['required', 'string'],
            'port' => ['required', 'numeric'],
            'database' => ['required', 'string'],
            'username' => ['required', 'string'],
            'password' => ['nullable', 'string'],
        ];

        return $request->validate($rules);
    }

    protected function validateConfiguration(Request $request): array
    {
        $rules = [
            'app_name' => ['required', 'string', 'max:255'],
            'app_url' => ['required', 'url'],
            'app_locale' => ['required', 'string', 'max:5'],
            'app_fallback_locale' => ['required', 'string', 'max:5'],
            'app_timezone' => ['required', 'string', 'max:64'],
            'app_env' => ['required', 'string'],
            'app_debug' => ['nullable', 'boolean'],
            'db_driver' => ['required', 'string'],
            'db_host' => ['required', 'string'],
            'db_port' => ['required', 'numeric'],
            'db_database' => ['required', 'string'],
            'db_username' => ['required', 'string'],
            'db_password' => ['nullable', 'string'],
            'cache_driver' => ['required', 'string'],
            'queue_connection' => ['required', 'string'],
            'mail_mailer' => ['required', 'string'],
            'mail_host' => ['nullable', 'string'],
            'mail_port' => ['nullable', 'numeric'],
            'mail_username' => ['nullable', 'string'],
            'mail_password' => ['nullable', 'string'],
            'mail_encryption' => ['nullable', 'string'],
            'mail_from_address' => ['nullable', 'email'],
            'mail_from_name' => ['nullable', 'string'],
            'admin_name' => ['required', 'string'],
            'admin_email' => ['required', 'email'],
            'admin_password' => ['nullable', 'string', 'min:8'],
        ];

        $data = $request->validate($rules);

        foreach ($data as $key => $value) {
            if (is_string($value) && $value === '') {
                $data[$key] = null;
            }
        }

        $data['app_debug'] = (bool) ($data['app_debug'] ?? false);

        return $data;
    }

    /**
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    protected function mapToEnv(array $data): array
    {
        return [
            'APP_NAME' => $data['app_name'],
            'APP_ENV' => $data['app_env'],
            'APP_DEBUG' => $data['app_debug'],
            'APP_URL' => $data['app_url'],
            'APP_LOCALE' => $data['app_locale'],
            'APP_FALLBACK_LOCALE' => $data['app_fallback_locale'],
            'APP_TIMEZONE' => $data['app_timezone'],
            'DB_CONNECTION' => $data['db_driver'],
            'DB_HOST' => $data['db_host'],
            'DB_PORT' => (string) $data['db_port'],
            'DB_DATABASE' => $data['db_database'],
            'DB_USERNAME' => $data['db_username'],
            'DB_PASSWORD' => $data['db_password'],
            'CACHE_DRIVER' => $data['cache_driver'],
            'QUEUE_CONNECTION' => $data['queue_connection'],
            'MAIL_MAILER' => $data['mail_mailer'],
            'MAIL_HOST' => $data['mail_host'],
            'MAIL_PORT' => $data['mail_port'] !== null ? (string) $data['mail_port'] : null,
            'MAIL_USERNAME' => $data['mail_username'],
            'MAIL_PASSWORD' => $data['mail_password'],
            'MAIL_ENCRYPTION' => $data['mail_encryption'],
            'MAIL_FROM_ADDRESS' => $data['mail_from_address'],
            'MAIL_FROM_NAME' => $data['mail_from_name'],
        ];
    }
}
