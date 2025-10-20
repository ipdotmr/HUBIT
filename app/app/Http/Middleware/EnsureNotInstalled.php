<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureNotInstalled
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $marker = config('installer.marker_path');

        if ($marker && file_exists($marker)) {
            abort(404);
        }

        return $next($request);
    }
}
