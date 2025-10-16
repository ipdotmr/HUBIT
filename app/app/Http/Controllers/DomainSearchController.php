<?php

namespace App\Http\Controllers;

use App\Services\Domain\DomainService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DomainSearchController extends Controller
{
    public function __construct(
        protected DomainService $domainService
    ) {}

    public function index(): Response
    {
        return Inertia::render('Domains/Search');
    }

    public function search(Request $request)
    {
        $validated = $request->validate([
            'fqdn' => 'required|string|regex:/^[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/',
            'registrar' => 'nullable|string|in:namecheap,namecom,coccaep',
        ]);

        $result = $this->domainService->search(
            $validated['fqdn'],
            $validated['registrar'] ?? null
        );

        return response()->json($result);
    }

    public function pricing(Request $request)
    {
        $tlds = [
            'com' => ['register' => 10.99, 'renew' => 12.99, 'transfer' => 10.99],
            'net' => ['register' => 11.99, 'renew' => 13.99, 'transfer' => 11.99],
            'org' => ['register' => 12.99, 'renew' => 14.99, 'transfer' => 12.99],
            'io' => ['register' => 39.99, 'renew' => 49.99, 'transfer' => 39.99],
            'co' => ['register' => 24.99, 'renew' => 29.99, 'transfer' => 24.99],
            'mr' => ['register' => 49.99, 'renew' => 59.99, 'transfer' => 49.99],
        ];

        return response()->json($tlds);
    }
}
