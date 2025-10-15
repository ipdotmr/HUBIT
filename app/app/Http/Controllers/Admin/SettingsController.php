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
     * Show company settings page
     */
    public function company()
    {
        return Inertia::render('Admin/Settings/Company', [
            'settings' => $this->getAllSettings(),
            'auditLogs' => $this->settingsService->getAuditLogsForKeys([
                'company.name', 'company.legal_name', 'company.tax_id', 'company.address',
                'company.logo', 'company.logo_dark', 'invoice.prefix', 'invoice.currency',
                'invoice.tax_rate', 'invoice.footer',
            ]),
        ]);
    }

    /**
     * Show themes settings page
     */
    public function themes()
    {
        return Inertia::render('Admin/Settings/Themes', [
            'settings' => $this->getAllSettings(),
            'auditLogs' => $this->settingsService->getAuditLogsForKeys([
                'theme.default', 'theme.allow_user_selection', 'theme.rtl_enabled', 'theme.custom_css',
            ]),
        ]);
    }

    /**
     * Show Stripe settings page
     */
    public function stripe()
    {
        return Inertia::render('Admin/Settings/StripePayments', [
            'settings' => $this->getAllSettings(),
            'auditLogs' => $this->settingsService->getAuditLogsForKeys([
                'stripe.mode', 'stripe.publishable_key', 'stripe.secret_key', 'stripe.webhook_secret',
            ]),
        ]);
    }

    /**
     * Show email settings page
     */
    public function email()
    {
        return Inertia::render('Admin/Settings/Email', [
            'settings' => $this->getAllSettings(),
            'auditLogs' => $this->settingsService->getAuditLogsForKeys([
                'mail.mailer', 'mail.host', 'mail.port', 'mail.username', 'mail.password',
                'mail.encryption', 'mail.from_address', 'mail.from_name',
            ]),
        ]);
    }

    /**
     * Show PayPal settings page
     */
    public function paypal()
    {
        return Inertia::render('Admin/Settings/PayPalPayments', [
            'settings' => $this->getAllSettings(),
            'auditLogs' => $this->settingsService->getAuditLogsForKeys([
                'paypal.mode', 'paypal.client_id', 'paypal.client_secret', 'paypal.webhook_id',
            ]),
        ]);
    }

    /**
     * Show cPanel settings page
     */
    public function cpanel()
    {
        return Inertia::render('Admin/Settings/CPanelProvisioning', [
            'settings' => $this->getAllSettings(),
            'auditLogs' => $this->settingsService->getAuditLogsForKeys([
                'cpanel.host', 'cpanel.api_token', 'cpanel.use_ssl', 'cpanel.default_package',
                'cpanel.nameserver1', 'cpanel.nameserver2', 'cpanel.nameserver3', 'cpanel.nameserver4',
            ]),
        ]);
    }

    /**
     * Show Plesk settings page
     */
    public function plesk()
    {
        return Inertia::render('Admin/Settings/PleskProvisioning', [
            'settings' => $this->getAllSettings(),
            'auditLogs' => $this->settingsService->getAuditLogsForKeys([
                'plesk.host', 'plesk.api_key', 'plesk.use_ssl', 'plesk.default_plan',
                'plesk.auth_type', 'plesk.username', 'plesk.password',
            ]),
        ]);
    }

    /**
     * Show Namecheap settings page
     */
    public function namecheap()
    {
        return Inertia::render('Admin/Settings/NamecheapDomains', [
            'settings' => $this->getAllSettings(),
            'auditLogs' => $this->settingsService->getAuditLogsForKeys([
                'namecheap.api_user', 'namecheap.api_key', 'namecheap.client_ip', 'namecheap.sandbox',
            ]),
        ]);
    }

    /**
     * Show ResellerClub settings page
     */
    public function resellerclub()
    {
        return Inertia::render('Admin/Settings/ResellerClubDomains', [
            'settings' => $this->getAllSettings(),
            'auditLogs' => $this->settingsService->getAuditLogsForKeys([
                'resellerclub.reseller_id', 'resellerclub.api_key', 'resellerclub.mode',
            ]),
        ]);
    }

    /**
     * Show Name.com settings page
     */
    public function namecom()
    {
        return Inertia::render('Admin/Settings/NamecomDomains', [
            'settings' => $this->getAllSettings(),
            'auditLogs' => $this->settingsService->getAuditLogsForKeys([
                'namecom.api_username', 'namecom.api_token', 'namecom.mode', 'namecom.sandbox_url',
            ]),
        ]);
    }

    /**
     * Show Coccaep settings page
     */
    public function coccaep()
    {
        return Inertia::render('Admin/Settings/CoccaepDomains', [
            'settings' => $this->getAllSettings(),
            'auditLogs' => $this->settingsService->getAuditLogsForKeys([
                'coccaep.api_base_url', 'coccaep.username', 'coccaep.password', 'coccaep.registrar_code',
                'coccaep.enabled_tlds', 'coccaep.whois_languages',
            ]),
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
            return back()->with('error', 'Failed to update settings: '.$e->getMessage());
        }
    }

    /**
     * Show Payment Accounts settings page
     */
    public function paymentAccounts()
    {
        $accounts = \App\Models\PaymentAccount::with('currency')->get();

        return Inertia::render('Admin/Settings/PaymentAccounts', [
            'accounts' => $accounts,
            'currencies' => \App\Models\Currency::where('enabled', true)->get(),
            'settings' => $this->getAllSettings(),
        ]);
    }

    /**
     * Show Exchange Rates settings page
     */
    public function exchangeRates()
    {
        $rates = \App\Models\ExchangeRate::with(['baseCurrency', 'quoteCurrency'])
            ->latest('fetched_at')
            ->get();

        return Inertia::render('Admin/Settings/ExchangeRates', [
            'rates' => $rates,
            'settings' => $this->getAllSettings(),
            'auditLogs' => $this->settingsService->getAuditLogsForKeys([
                'currency.use_manual_rates', 'currency.rate_mru_usd', 'currency.rate_mru_eur', 'currency.rate_usd_eur',
            ]),
        ]);
    }

    /**
     * Show Localization/Currency settings page
     */
    public function localization()
    {
        return Inertia::render('Admin/Settings/Localization', [
            'currencies' => \App\Models\Currency::all(),
            'settings' => $this->getAllSettings(),
            'auditLogs' => $this->settingsService->getAuditLogsForKeys([
                'currency.default', 'currency.allowed', 'app.locale', 'app.timezone',
            ]),
        ]);
    }

    /**
     * Test connection
     */
    public function testConnection(Request $request)
    {
        $validated = $request->validate([
            'service' => 'required|string|in:stripe,paypal,cpanel,plesk,namecheap,resellerclub,namecom,coccaep,smtp,currency',
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
                'company.legal_name' => $this->settingsService->get('company.legal_name'),
                'company.address' => $this->settingsService->get('company.address'),
                'company.tax_id' => $this->settingsService->get('company.tax_id'),
                'company.logo' => $this->settingsService->get('company.logo'),
                'company.logo_dark' => $this->settingsService->get('company.logo_dark'),
                'invoice.prefix' => $this->settingsService->get('invoice.prefix', 'INV-'),
                'invoice.currency' => $this->settingsService->get('invoice.currency', 'USD'),
                'invoice.tax_rate' => $this->settingsService->get('invoice.tax_rate', 0),
                'invoice.footer' => $this->settingsService->get('invoice.footer'),
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
                'theme.rtl_enabled' => $this->settingsService->get('theme.rtl_enabled', true),
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
                'cpanel.nameserver3' => $this->settingsService->get('cpanel.nameserver3'),
                'cpanel.nameserver4' => $this->settingsService->get('cpanel.nameserver4'),
            ],
            'plesk' => [
                'plesk.host' => $this->settingsService->get('plesk.host'),
                'plesk.api_key' => $this->settingsService->get('plesk.api_key'),
                'plesk.auth_type' => $this->settingsService->get('plesk.auth_type', 'api_key'),
                'plesk.username' => $this->settingsService->get('plesk.username'),
                'plesk.password' => $this->settingsService->get('plesk.password'),
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
            'namecom' => [
                'namecom.api_username' => $this->settingsService->get('namecom.api_username'),
                'namecom.api_token' => $this->settingsService->get('namecom.api_token'),
                'namecom.mode' => $this->settingsService->get('namecom.mode', 'test'),
                'namecom.sandbox_url' => $this->settingsService->get('namecom.sandbox_url'),
            ],
            'coccaep' => [
                'coccaep.api_base_url' => $this->settingsService->get('coccaep.api_base_url', 'https://registry.coccaep.mr/api'),
                'coccaep.username' => $this->settingsService->get('coccaep.username'),
                'coccaep.password' => $this->settingsService->get('coccaep.password'),
                'coccaep.registrar_code' => $this->settingsService->get('coccaep.registrar_code'),
                'coccaep.enabled_tlds' => json_decode($this->settingsService->get('coccaep.enabled_tlds', '[ ".mr"]'), true),
                'coccaep.whois_languages' => json_decode($this->settingsService->get('coccaep.whois_languages', '["en","ar"]'), true),
            ],
            'currency' => [
                'currency.default' => $this->settingsService->get('currency.default', 'MRU'),
                'currency.allowed' => json_decode($this->settingsService->get('currency.allowed', '["MRU","USD","EUR"]'), true),
                'currency.use_manual_rates' => $this->settingsService->get('currency.use_manual_rates', true),
                'currency.rate_mru_usd' => $this->settingsService->get('currency.rate_mru_usd', 0.0274),
                'currency.rate_mru_eur' => $this->settingsService->get('currency.rate_mru_eur', 0.0250),
                'currency.rate_usd_eur' => $this->settingsService->get('currency.rate_usd_eur', 0.92),
            ],
        ];
    }
}
