<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Jobs\Domain\ToggleLockJob;
use App\Jobs\Domain\TogglePrivacyJob;
use App\Jobs\Domain\UpdateDnsRecordJob;
use App\Jobs\Domain\UpdateNameserversJob;
use App\Models\Domain;
use App\Models\DomainDnsRecord;
use App\Models\DomainRenewal;
use App\Services\AuditLogService;
use App\Services\InvoiceService;
use App\Services\PricingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class DomainController extends Controller
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

        $query = Domain::query()
            ->where('client_id', $client->id)
            ->with(['order', 'renewals' => fn ($q) => $q->latest()->limit(1)]);

        if ($request->filled('registrar')) {
            $query->where('registrar', $request->input('registrar'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('expiring_in_days')) {
            $days = (int) $request->input('expiring_in_days');
            $query->where('expires_at', '<=', now()->addDays($days))
                ->where('expires_at', '>=', now());
        }

        $domains = $query->latest()->paginate(20)->withQueryString();

        return Inertia::render('Client/Domains/Index', [
            'domains' => $domains,
            'filters' => $request->only(['registrar', 'status', 'expiring_in_days']),
        ]);
    }

    public function show(Domain $domain): Response
    {
        Gate::authorize('view', $domain);

        $domain->load([
            'client',
            'order',
            'dnsRecords' => fn ($q) => $q->orderBy('type')->orderBy('host'),
            'renewals' => fn ($q) => $q->latest()->limit(5),
        ]);

        return Inertia::render('Client/Domains/Show', [
            'domain' => $domain,
            'renewalPricing' => $this->pricing->getDomainRenewalPricing($domain),
        ]);
    }

    public function updateNameservers(Request $request, Domain $domain): RedirectResponse
    {
        Gate::authorize('update', $domain);

        $validator = Validator::make($request->all(), [
            'nameservers' => ['required', 'array', 'min:2', 'max:5'],
            'nameservers.*' => [
                'required',
                'string',
                'regex:/^[a-z0-9]([a-z0-9-]{0,61}[a-z0-9])?(\.[a-z0-9]([a-z0-9-]{0,61}[a-z0-9])?)*$/i',
            ],
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $nameservers = array_values(array_unique($request->input('nameservers')));

        if (count($nameservers) !== count($request->input('nameservers'))) {
            return back()->withErrors(['nameservers' => __('domains.nameservers_must_be_unique')]);
        }

        $oldNameservers = $domain->nameservers;

        UpdateNameserversJob::dispatch($domain, $nameservers)
            ->onQueue('registrar:'.$domain->registrar);

        $this->auditLog->log('domain.nameservers.updated', $domain, [
            'old' => $oldNameservers,
            'new' => $nameservers,
            'ip' => $request->ip(),
        ]);

        return back()->with('success', __('domains.nameservers_update_queued'));
    }

    public function storeDnsRecord(Request $request, Domain $domain): RedirectResponse
    {
        Gate::authorize('update', $domain);

        $validator = $this->validateDnsRecord($request);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $record = $domain->dnsRecords()->create($validator->validated());

        UpdateDnsRecordJob::dispatch($domain, $record, 'create')
            ->onQueue('registrar:'.$domain->registrar);

        $this->auditLog->log('domain.dns.created', $domain, [
            'record' => $record->only(['type', 'host', 'value', 'priority', 'ttl']),
            'ip' => $request->ip(),
        ]);

        return back()->with('success', __('domains.dns_record_created'));
    }

    public function updateDnsRecord(Request $request, Domain $domain, DomainDnsRecord $record): RedirectResponse
    {
        Gate::authorize('update', $domain);

        if ($record->domain_id !== $domain->id) {
            abort(404);
        }

        $validator = $this->validateDnsRecord($request);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $oldData = $record->only(['type', 'host', 'value', 'priority', 'ttl']);

        $record->update($validator->validated());

        UpdateDnsRecordJob::dispatch($domain, $record, 'update')
            ->onQueue('registrar:'.$domain->registrar);

        $this->auditLog->log('domain.dns.updated', $domain, [
            'old' => $oldData,
            'new' => $record->only(['type', 'host', 'value', 'priority', 'ttl']),
            'ip' => $request->ip(),
        ]);

        return back()->with('success', __('domains.dns_record_updated'));
    }

    public function destroyDnsRecord(Request $request, Domain $domain, DomainDnsRecord $record): RedirectResponse
    {
        Gate::authorize('update', $domain);

        if ($record->domain_id !== $domain->id) {
            abort(404);
        }

        $recordData = $record->only(['type', 'host', 'value', 'priority', 'ttl']);

        UpdateDnsRecordJob::dispatch($domain, $record, 'delete')
            ->onQueue('registrar:'.$domain->registrar);

        $record->delete();

        $this->auditLog->log('domain.dns.deleted', $domain, [
            'record' => $recordData,
            'ip' => $request->ip(),
        ]);

        return back()->with('success', __('domains.dns_record_deleted'));
    }

    public function updatePrivacy(Request $request, Domain $domain): RedirectResponse
    {
        Gate::authorize('update', $domain);

        $request->validate([
            'enabled' => ['required', 'boolean'],
        ]);

        $enabled = $request->boolean('enabled');

        TogglePrivacyJob::dispatch($domain, $enabled)
            ->onQueue('registrar:'.$domain->registrar);

        $this->auditLog->log('domain.privacy.toggled', $domain, [
            'old' => $domain->privacy_enabled,
            'new' => $enabled,
            'ip' => $request->ip(),
        ]);

        return back()->with('success', __('domains.privacy_update_queued'));
    }

    public function updateLock(Request $request, Domain $domain): RedirectResponse
    {
        Gate::authorize('update', $domain);

        $request->validate([
            'enabled' => ['required', 'boolean'],
        ]);

        $enabled = $request->boolean('enabled');

        ToggleLockJob::dispatch($domain, $enabled)
            ->onQueue('registrar:'.$domain->registrar);

        $this->auditLog->log('domain.lock.toggled', $domain, [
            'old' => $domain->lock_enabled,
            'new' => $enabled,
            'ip' => $request->ip(),
        ]);

        return back()->with('success', __('domains.lock_update_queued'));
    }

    public function renew(Request $request, Domain $domain): RedirectResponse
    {
        Gate::authorize('update', $domain);

        $request->validate([
            'years' => ['required', 'integer', 'min:1', 'max:10'],
        ]);

        $years = (int) $request->input('years');
        $pricing = $this->pricing->getDomainRenewalPricing($domain, $years);

        $renewal = DomainRenewal::create([
            'domain_id' => $domain->id,
            'years' => $years,
            'status' => 'pending',
            'old_expiry' => $domain->expires_at,
            'new_expiry' => $domain->expires_at?->copy()->addYears($years),
        ]);

        $invoice = $this->invoiceService->createInvoiceForDomainRenewal($renewal, $pricing);

        $renewal->update(['invoice_id' => $invoice->id]);

        $this->auditLog->log('domain.renewal.initiated', $domain, [
            'years' => $years,
            'invoice_id' => $invoice->id,
            'amount' => $pricing['total'],
            'ip' => $request->ip(),
        ]);

        return redirect()->route('invoices.show', $invoice->id)
            ->with('success', __('domains.renewal_invoice_created'));
    }

    protected function validateDnsRecord(Request $request): \Illuminate\Contracts\Validation\Validator
    {
        $rules = [
            'type' => ['required', Rule::in(['A', 'AAAA', 'CNAME', 'MX', 'TXT', 'CAA'])],
            'host' => ['required', 'string', 'max:255'],
            'value' => ['required', 'string', 'max:2048'],
            'ttl' => ['required', 'integer', 'min:60', 'max:86400'],
            'priority' => ['nullable', 'integer', 'min:0', 'max:65535'],
        ];

        $messages = [
            'type.in' => __('domains.dns_type_invalid'),
            'ttl.min' => __('domains.dns_ttl_min', ['min' => 60]),
            'ttl.max' => __('domains.dns_ttl_max', ['max' => 86400]),
        ];

        return Validator::make($request->all(), $rules, $messages)->after(function ($validator) use ($request) {
            $type = $request->input('type');
            $value = $request->input('value');

            if ($type === 'A' && ! filter_var($value, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
                $validator->errors()->add('value', __('domains.dns_a_invalid_ipv4'));
            }

            if ($type === 'AAAA' && ! filter_var($value, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
                $validator->errors()->add('value', __('domains.dns_aaaa_invalid_ipv6'));
            }

            if (in_array($type, ['MX', 'SRV']) && ! $request->filled('priority')) {
                $validator->errors()->add('priority', __('domains.dns_priority_required'));
            }

            if ($type === 'CAA') {
                if (! preg_match('/^(0|128)\s+(issue|issuewild|iodef)\s+".+"$/', $value)) {
                    $validator->errors()->add('value', __('domains.dns_caa_invalid_format'));
                }
            }
        });
    }
}
