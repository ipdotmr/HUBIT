<?php

namespace Database\Seeders;

use App\Services\SettingsService;
use Illuminate\Database\Seeder;

class TestCredentialsSeeder extends Seeder
{
    /**
     * Seed test/sandbox credentials for all integrations via Settings Console
     */
    public function run(): void
    {
        $settings = app(SettingsService::class);

        $settings->setMany([
            'stripe.publishable_key' => [
                'value' => env('STRIPE_TEST_KEY', ''),
                'type' => 'string',
                'is_secret' => false,
            ],
            'stripe.secret_key' => [
                'value' => env('STRIPE_TEST_SECRET', ''),
                'type' => 'string',
                'is_secret' => true,
            ],
            'stripe.webhook_secret' => [
                'value' => env('STRIPE_WEBHOOK_SECRET', ''),
                'type' => 'string',
                'is_secret' => true,
            ],
            'stripe.mode' => [
                'value' => 'test',
                'type' => 'string',
                'is_secret' => false,
            ],

            'paypal.client_id' => [
                'value' => env('PAYPAL_SANDBOX_CLIENT_ID', ''),
                'type' => 'string',
                'is_secret' => false,
            ],
            'paypal.client_secret' => [
                'value' => env('PAYPAL_SANDBOX_CLIENT_SECRET', ''),
                'type' => 'string',
                'is_secret' => true,
            ],
            'paypal.webhook_id' => [
                'value' => env('PAYPAL_WEBHOOK_ID', ''),
                'type' => 'string',
                'is_secret' => false,
            ],
            'paypal.mode' => [
                'value' => 'sandbox',
                'type' => 'string',
                'is_secret' => false,
            ],

            'cpanel.host' => [
                'value' => env('CPANEL_HOST', 'test.whm.local'),
                'type' => 'string',
                'is_secret' => false,
            ],
            'cpanel.api_token' => [
                'value' => env('CPANEL_API_TOKEN', ''),
                'type' => 'string',
                'is_secret' => true,
            ],
            'cpanel.use_ssl' => [
                'value' => 'true',
                'type' => 'boolean',
                'is_secret' => false,
            ],
            'cpanel.default_package' => [
                'value' => 'HUBIT_TEST',
                'type' => 'string',
                'is_secret' => false,
            ],

            'plesk.host' => [
                'value' => env('PLESK_HOST', 'test.plesk.local'),
                'type' => 'string',
                'is_secret' => false,
            ],
            'plesk.api_key' => [
                'value' => env('PLESK_API_KEY', ''),
                'type' => 'string',
                'is_secret' => true,
            ],
            'plesk.use_ssl' => [
                'value' => 'true',
                'type' => 'boolean',
                'is_secret' => false,
            ],

            'namecheap.api_user' => [
                'value' => env('NAMECHEAP_API_USER', ''),
                'type' => 'string',
                'is_secret' => false,
            ],
            'namecheap.api_key' => [
                'value' => env('NAMECHEAP_API_KEY', ''),
                'type' => 'string',
                'is_secret' => true,
            ],
            'namecheap.username' => [
                'value' => env('NAMECHEAP_USERNAME', ''),
                'type' => 'string',
                'is_secret' => false,
            ],
            'namecheap.sandbox' => [
                'value' => 'true',
                'type' => 'boolean',
                'is_secret' => false,
            ],

            'namecom.username' => [
                'value' => env('NAMECOM_USERNAME', ''),
                'type' => 'string',
                'is_secret' => false,
            ],
            'namecom.api_token' => [
                'value' => env('NAMECOM_API_TOKEN', ''),
                'type' => 'string',
                'is_secret' => true,
            ],
            'namecom.test_mode' => [
                'value' => 'true',
                'type' => 'boolean',
                'is_secret' => false,
            ],

            'resellerclub.reseller_id' => [
                'value' => env('RESELLERCLUB_RESELLER_ID', ''),
                'type' => 'string',
                'is_secret' => false,
            ],
            'resellerclub.api_key' => [
                'value' => env('RESELLERCLUB_API_KEY', ''),
                'type' => 'string',
                'is_secret' => true,
            ],
            'resellerclub.test_mode' => [
                'value' => 'true',
                'type' => 'boolean',
                'is_secret' => false,
            ],

            'coccaep.username' => [
                'value' => env('COCCAEP_USERNAME', ''),
                'type' => 'string',
                'is_secret' => false,
            ],
            'coccaep.password' => [
                'value' => env('COCCAEP_PASSWORD', ''),
                'type' => 'string',
                'is_secret' => true,
            ],
            'coccaep.test_mode' => [
                'value' => 'true',
                'type' => 'boolean',
                'is_secret' => false,
            ],

            'mail.driver' => [
                'value' => 'log',
                'type' => 'string',
                'is_secret' => false,
            ],
            'mail.from_address' => [
                'value' => 'test@hubit.test',
                'type' => 'string',
                'is_secret' => false,
            ],
            'mail.from_name' => [
                'value' => 'HUBIT Test',
                'type' => 'string',
                'is_secret' => false,
            ],

            'company.name' => [
                'value' => 'HUBIT Demo',
                'type' => 'string',
                'is_secret' => false,
            ],
            'company.tax_rate' => [
                'value' => '16',
                'type' => 'string',
                'is_secret' => false,
            ],
        ]);

        $this->command->info('✅ Test credentials seeded successfully via Settings Console');
        $this->command->info('All credentials stored encrypted and accessible via /managit/settings/*');
    }
}
