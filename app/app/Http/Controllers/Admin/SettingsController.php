<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\SettingsService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SettingsController extends Controller
{
    public function __construct(
        private SettingsService $settingsService
    ) {}

    /**
     * Show settings console
     */
    public function index()
    {
        return Inertia::render('Admin/Settings/Index', [
            'settings' => $this->getAllSettings(),
            'auditLogs' => $this->settingsService->getAuditLogs(50),
        ]);
    }

    /**
     * Update settings
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'settings' => 'required|array',
            'settings.*.key' => 'required|string',
            'settings.*.value' => 'nullable',
            'settings.*.type' => 'required|string|in:string,int,bool,json,array',
            'settings.*.is_secret' => 'sometimes|boolean',
        ]);

        try {
            foreach ($validated['settings'] as $setting) {
                $this->settingsService->set(
                    $setting['key'],
                    $setting['value'],
                    $setting['type'],
                    $setting['is_secret'] ?? false,
                    $request->user()->id
                );
            }

            return back()->with('success', 'Settings updated successfully');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to update settings: ' . $e->getMessage());
        }
    }

    /**
     * Test connection
     */
    public function testConnection(Request $request)
    {
        $validated = $request->validate([
            'service' => 'required|string|in:stripe,paypal,cpanel,plesk,namecheap,resellerclub,smtp',
        ]);

        $result = $this->settingsService->testConnection($validated['service']);

        return response()->json($result);
    }

    /**
     * Get all settings organized by section
     */
    private function getAllSettings()
    {
        return [
            'general' => [
                'company.name' => $this->settingsService->get('company.name'),
                'company.address' => $this->settingsService->get('company.address'),
                'company.tax_id' => $this->settingsService->get('company.tax_id'),
                'company.logo' => $this->settingsService->get('company.logo'),
                'invoice.currency' => $this->settingsService->get('invoice.currency', 'USD'),
                'invoice.tax_rate' => $this->settingsService->get('invoice.tax_rate', 0),
            ],
            'localization' => [
                'app.locale' => $this->settingsService->get('app.locale', 'en'),
                'app.timezone' => $this->settingsService->get('app.timezone', 'UTC'),
                'app.date_format' => $this->settingsService->get('app.date_format', 'Y-m-d'),
                'app.time_format' => $this->settingsService->get('app.time_format', 'H:i:s'),
            ],
            'theme' => [
                'theme.default' => $this->settingsService->get('theme.default', 'ipmr'),
                'theme.allow_user_selection' => $this->settingsService->get('theme.allow_user_selection', true),
                'theme.custom_css' => $this->settingsService->get('theme.custom_css'),
            ],
            'email' => [
                'mail.mailer' => $this->settingsService->get('mail.mailer', 'smtp'),
                'mail.host' => $this->settingsService->get('mail.host'),
                'mail.port' => $this->settingsService->get('mail.port', 587),
                'mail.username' => $this->settingsService->get('mail.username'),
                'mail.password' => $this->settingsService->get('mail.password'),
                'mail.encryption' => $this->settingsService->get('mail.encryption', 'tls'),
                'mail.from_address' => $this->settingsService->get('mail.from_address'),
                'mail.from_name' => $this->settingsService->get('mail.from_name'),
            ],
            'stripe' => [
                'stripe.mode' => $this->settingsService->get('stripe.mode', 'test'),
                'stripe.publishable_key' => $this->settingsService->get('stripe.publishable_key'),
                'stripe.secret_key' => $this->settingsService->get('stripe.secret_key'),
                'stripe.webhook_secret' => $this->settingsService->get('stripe.webhook_secret'),
            ],
            'paypal' => [
                'paypal.mode' => $this->settingsService->get('paypal.mode', 'sandbox'),
                'paypal.client_id' => $this->settingsService->get('paypal.client_id'),
                'paypal.client_secret' => $this->settingsService->get('paypal.client_secret'),
                'paypal.webhook_id' => $this->settingsService->get('paypal.webhook_id'),
            ],
            'cpanel' => [
                'cpanel.host' => $this->settingsService->get('cpanel.host'),
                'cpanel.api_token' => $this->settingsService->get('cpanel.api_token'),
                'cpanel.use_ssl' => $this->settingsService->get('cpanel.use_ssl', true),
                'cpanel.default_package' => $this->settingsService->get('cpanel.default_package'),
                'cpanel.nameserver1' => $this->settingsService->get('cpanel.nameserver1'),
                'cpanel.nameserver2' => $this->settingsService->get('cpanel.nameserver2'),
            ],
            'plesk' => [
                'plesk.host' => $this->settingsService->get('plesk.host'),
                'plesk.api_key' => $this->settingsService->get('plesk.api_key'),
                'plesk.use_ssl' => $this->settingsService->get('plesk.use_ssl', true),
                'plesk.default_plan' => $this->settingsService->get('plesk.default_plan'),
            ],
            'namecheap' => [
                'namecheap.api_user' => $this->settingsService->get('namecheap.api_user'),
                'namecheap.api_key' => $this->settingsService->get('namecheap.api_key'),
                'namecheap.client_ip' => $this->settingsService->get('namecheap.client_ip'),
                'namecheap.sandbox' => $this->settingsService->get('namecheap.sandbox', true),
            ],
            'resellerclub' => [
                'resellerclub.reseller_id' => $this->settingsService->get('resellerclub.reseller_id'),
                'resellerclub.api_key' => $this->settingsService->get('resellerclub.api_key'),
                'resellerclub.mode' => $this->settingsService->get('resellerclub.mode', 'test'),
            ],
        ];
    }
}
