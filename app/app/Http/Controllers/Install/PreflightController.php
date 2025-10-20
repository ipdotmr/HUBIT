<?php

namespace App\Http\Controllers\Install;

use App\Support\Install\PreflightCheck;
use Illuminate\Http\JsonResponse;

class PreflightController
{
    public function __invoke(): JsonResponse
    {
        $results = PreflightCheck::run();

        return response()->json($results);
    }
}
