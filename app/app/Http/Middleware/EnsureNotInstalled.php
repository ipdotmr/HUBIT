<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Database\DatabaseManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class EnsureNotInstalled
{
    public function __construct(protected DatabaseManager $database)
    {
    }

    /**
     * @param Closure(Request): mixed $next
     */
    public function handle(Request $request, Closure $next)
    {
        if ($this->isLocked()) {
            throw new NotFoundHttpException();
        }

        $token = config('installer.token');
        if ($token) {
            $session = $request->session();
            if (! $session->get('installer.token.valid')) {
                $provided = $request->query('token')
                    ?? $request->header('X-Install-Token')
                    ?? $request->input('token');

                if ($provided === $token) {
                    $session->put('installer.token.valid', true);

                    if ($request->has('token')) {
                        return redirect()->to($request->url());
                    }
                } else {
                    if ($request->expectsJson()) {
                        return response()->json([
                            'message' => 'Installer token required.',
                        ], 403);
                    }

                    return response()->view('install.token', [
                        'error' => $provided ? 'Invalid installer token.' : null,
                    ]);
                }
            }
        }

        return $next($request);
    }

    protected function isLocked(): bool
    {
        $lockExists = file_exists(storage_path('framework/install.lock'));

        if ($lockExists) {
            return true;
        }

        if (! app()->environment('production')) {
            return false;
        }

        if (! empty(config('app.key'))) {
            try {
                if (Schema::hasTable('migrations')) {
                    $count = $this->database->table('migrations')->count();

                    return $count > 0;
                }
            } catch (\Throwable $e) {
                return false;
            }
        }

        return false;
    }
}
