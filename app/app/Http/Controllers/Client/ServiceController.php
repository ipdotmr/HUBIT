<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Jobs\Service\ResetPasswordJob;
use App\Jobs\Service\SyncServiceJob;
use App\Jobs\Service\UpgradeServiceJob;
use App\Models\Product;
use App\Models\Service;
use App\Services\AuditLogService;
use App\Services\InvoiceService;
use App\Services\PricingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;

class ServiceController extends Controller
{
    public function __construct(
        protected AuditLogService $auditLog,
        protected InvoiceService $invoiceService,
        protected PricingService $pricing
    ) {
    }

    public function index(Request $request): Response
    {
        $client = Auth::user()->client;

        $query = Service::query()
            ->where('client_id', $client->id)
            ->with(['product', 'order']);

        if ($request->filled('product')) {
            $query->whereHas('product', fn ($q) => $q->where('name', 'like', '%'.$request->input('product').'%'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        $services = $query->latest()->paginate(20)->withQueryString();

        return Inertia::render('Client/Services/Index', [
            'services' => $services,
            'filters' => $request->only(['product', 'status']),
        ]);
    }

    public function show(Service $service): Response
    {
        Gate::authorize('view', $service);

        $service->load(['client', 'product', 'order']);

        $upgradePlans = null;
        if ($service->status === 'active' && $service->product) {
            $upgradePlans = Product::where('group', $service->product->group)
                ->where('id', '!=', $service->product_id)
                ->where('is_active', true)
                ->get()
                ->map(fn ($product) => [
                    'id' => $product->id,
                    'name' => $product->name,
                    'description' => $product->description,
                    'pricing' => $this->pricing->getUpgradePricing($service, $product),
                ]);
        }

        return Inertia::render('Client/Services/Show', [
            'service' => $service,
            'upgradePlans' => $upgradePlans,
            'panelUrl' => $this->getPanelUrl($service),
        ]);
    }

    public function resetPassword(Request $request, Service $service): RedirectResponse
    {
        Gate::authorize('update', $service);

        if ($service->status !== 'active') {
            return back()->withErrors(['service' => 'Service must be active to reset password']);
        }

        $request->validate([
            'password' => ['required', 'string', 'min:12', 'max:64'],
        ]);

        ResetPasswordJob::dispatch($service, $request->input('password'))
            ->onQueue('provisioner:'.$service->provisioner);

        $this->auditLog->log('service.password_reset', $service, [
            'ip' => $request->ip(),
        ]);

        return back()->with('success', 'Password reset has been queued');
    }

    public function sync(Request $request, Service $service): RedirectResponse
    {
        Gate::authorize('update', $service);

        if (! in_array($service->status, ['active', 'suspended'])) {
            return back()->withErrors(['service' => 'Service must be active or suspended to sync']);
        }

        SyncServiceJob::dispatch($service)
            ->onQueue('provisioner:'.$service->provisioner);

        $this->auditLog->log('service.sync_requested', $service, [
            'ip' => $request->ip(),
        ]);

        return back()->with('success', 'Service sync has been queued');
    }

    public function suspend(Request $request, Service $service): RedirectResponse
    {
        Gate::authorize('suspend', $service);

        if ($service->status !== 'active') {
            return back()->withErrors(['service' => 'Only active services can be suspended']);
        }

        $service->update(['status' => 'pending_suspension']);

        SyncServiceJob::dispatch($service, 'suspend')
            ->onQueue('provisioner:'.$service->provisioner);

        $this->auditLog->log('service.suspend_requested', $service, [
            'ip' => $request->ip(),
        ]);

        return back()->with('success', 'Service suspension has been queued');
    }

    public function unsuspend(Request $request, Service $service): RedirectResponse
    {
        Gate::authorize('suspend', $service);

        if ($service->status !== 'suspended') {
            return back()->withErrors(['service' => 'Only suspended services can be unsuspended']);
        }

        $service->update(['status' => 'pending_unsuspension']);

        SyncServiceJob::dispatch($service, 'unsuspend')
            ->onQueue('provisioner:'.$service->provisioner);

        $this->auditLog->log('service.unsuspend_requested', $service, [
            'ip' => $request->ip(),
        ]);

        return back()->with('success', 'Service unsuspension has been queued');
    }

    public function upgrade(Request $request, Service $service): RedirectResponse
    {
        Gate::authorize('update', $service);

        $request->validate([
            'product_id' => ['required', 'exists:products,id'],
        ]);

        $newProduct = Product::findOrFail($request->input('product_id'));

        if ($newProduct->group !== $service->product->group) {
            return back()->withErrors(['product_id' => 'Cannot upgrade to a different product group']);
        }

        if (! $newProduct->is_active) {
            return back()->withErrors(['product_id' => 'Selected product is not available']);
        }

        $pricing = $this->pricing->getUpgradePricing($service, $newProduct);

        $invoice = $this->invoiceService->createInvoiceForUpgrade($service, $newProduct, $pricing);

        $this->auditLog->log('service.upgrade_initiated', $service, [
            'old_product_id' => $service->product_id,
            'new_product_id' => $newProduct->id,
            'invoice_id' => $invoice->id,
            'amount' => $pricing['total'],
            'ip' => $request->ip(),
        ]);

        return redirect()->route('invoices.show', $invoice->id)
            ->with('success', 'Upgrade invoice created successfully');
    }

    protected function getPanelUrl(Service $service): ?string
    {
        if ($service->status !== 'active' || ! $service->provision_ref) {
            return null;
        }

        if ($service->provisioner === 'cpanel') {
            $host = $service->provision_ref['host'] ?? null;
            $username = $service->provision_ref['username'] ?? null;

            if ($host && $username) {
                return "https://{$host}:2083";
            }
        }

        if ($service->provisioner === 'plesk') {
            $host = $service->provision_ref['host'] ?? null;

            if ($host) {
                return "https://{$host}:8443";
            }
        }

        return null;
    }
}
