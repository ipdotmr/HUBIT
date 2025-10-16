<?php

namespace App\Services\Testing;

use App\Services\Testing\Mocks\MockPayPalClient;
use App\Services\Testing\Mocks\MockProvisionerClient;
use App\Services\Testing\Mocks\MockRegistrarClient;
use App\Services\Testing\Mocks\MockStripeClient;

class MockFactory
{
    /**
     * Determine if mocks should be used
     */
    public static function shouldUseMocks(): bool
    {
        $settings = app(\App\Services\SettingsService::class);
        $settingValue = $settings->get('testing.use_mocks');

        if ($settingValue !== null) {
            return filter_var($settingValue, FILTER_VALIDATE_BOOLEAN);
        }

        return env('E2E_USE_MOCKS', false);
    }

    /**
     * Get Stripe client (real or mock)
     */
    public static function stripe()
    {
        if (self::shouldUseMocks()) {
            return new MockStripeClient;
        }

        \Stripe\Stripe::setApiKey(config('services.stripe.secret'));

        return new \Stripe\StripeClient(config('services.stripe.secret'));
    }

    /**
     * Get PayPal client (real or mock)
     */
    public static function paypal()
    {
        if (self::shouldUseMocks()) {
            return new MockPayPalClient;
        }

        throw new \Exception('Real PayPal client not yet implemented');
    }

    /**
     * Get registrar client (real or mock)
     */
    public static function registrar(string $registrar = 'namecheap')
    {
        if (self::shouldUseMocks()) {
            return new MockRegistrarClient($registrar);
        }

        return app("App\\Services\\Registrars\\{$registrar}Registrar");
    }

    /**
     * Get provisioner client (real or mock)
     */
    public static function provisioner(string $provider = 'cpanel')
    {
        if (self::shouldUseMocks()) {
            return new MockProvisionerClient($provider);
        }

        return app("App\\Services\\Provisioning\\{$provider}Provisioner");
    }
}
