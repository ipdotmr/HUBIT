<?php

namespace App\Services;

use App\Models\SystemSetting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class SettingsService
{
    private const CACHE_PREFIX = 'settings:';

    private const CACHE_TTL = 3600; // 1 hour

    /**
     * Get a setting value with caching
     */
    public function get($key, $default = null)
    {
        return Cache::remember(
            self::CACHE_PREFIX.$key,
            self::CACHE_TTL,
            function () use ($key, $default) {
                return SystemSetting::getValue($key, $default);
            }
        );
    }

    /**
     * Set a setting value and clear cache
     */
    public function set($key, $value, $type = 'string', $isSecret = false, $userId = null)
    {
        $setting = SystemSetting::setValue($key, $value, $type, $isSecret, $userId);

        Cache::forget(self::CACHE_PREFIX.$key);

        $this->logSettingChange($setting, null, $value, $userId);

        return $setting;
    }

    /**
     * Get multiple settings at once
     */
    public function getMany(array $keys)
    {
        $results = [];
        foreach ($keys as $key) {
            $results[$key] = $this->get($key);
        }

        return $results;
    }

    /**
     * Set multiple settings at once
     */
    public function setMany(array $settings, $userId = null)
    {
        foreach ($settings as $key => $data) {
            $value = $data['value'] ?? $data;
            $type = $data['type'] ?? 'string';
            $isSecret = $data['is_secret'] ?? false;

            $this->set($key, $value, $type, $isSecret, $userId);
        }
    }

    /**
     * Get all settings grouped by section
     */
    public function getAllGrouped()
    {
        $settings = SystemSetting::all();

        $grouped = [];
        foreach ($settings as $setting) {
            $section = $this->extractSection($setting->key);
            if (! isset($grouped[$section])) {
                $grouped[$section] = [];
            }
            $grouped[$section][] = [
                'key' => $setting->key,
                'value' => $setting->is_secret ? '***REDACTED***' : $setting->value,
                'type' => $setting->type,
                'is_secret' => $setting->is_secret,
                'meta' => $setting->meta,
            ];
        }

        return $grouped;
    }

    /**
     * Test a connection (Stripe, PayPal, cPanel, etc.)
     */
    public function testConnection($service)
    {
        try {
            return match ($service) {
                'stripe' => $this->testStripeConnection(),
                'paypal' => $this->testPayPalConnection(),
                'cpanel' => $this->testCPanelConnection(),
                'plesk' => $this->testPleskConnection(),
                'namecheap' => $this->testNamecheapConnection(),
                'resellerclub' => $this->testResellerClubConnection(),
                'smtp' => $this->testSMTPConnection(),
                default => [
                    'success' => false,
                    'message' => 'Unknown service: '.$service,
                ],
            };
        } catch (\Exception $e) {
            Log::error('Connection test failed: '.$e->getMessage());

            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Test Stripe connection
     */
    private function testStripeConnection()
    {
        $secretKey = $this->get('stripe.secret_key');

        if (! $secretKey) {
            return [
                'success' => false,
                'message' => 'Stripe secret key not configured',
            ];
        }

        try {
            \Stripe\Stripe::setApiKey($secretKey);
            \Stripe\Balance::retrieve();

            return [
                'success' => true,
                'message' => 'Successfully connected to Stripe',
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Stripe connection failed: '.$e->getMessage(),
            ];
        }
    }

    /**
     * Test PayPal connection
     */
    private function testPayPalConnection()
    {
        $clientId = $this->get('paypal.client_id');
        $clientSecret = $this->get('paypal.client_secret');

        if (! $clientId || ! $clientSecret) {
            return [
                'success' => false,
                'message' => 'PayPal credentials not configured',
            ];
        }

        return [
            'success' => true,
            'message' => 'PayPal credentials configured (full test pending)',
        ];
    }

    /**
     * Test cPanel connection
     */
    private function testCPanelConnection()
    {
        $host = $this->get('cpanel.host');
        $apiToken = $this->get('cpanel.api_token');

        if (! $host || ! $apiToken) {
            return [
                'success' => false,
                'message' => 'cPanel credentials not configured',
            ];
        }

        try {
            $cpanelService = app(\App\Services\Provisioning\CpanelProvisioner::class);
            $packages = $cpanelService->listPackages();

            return [
                'success' => true,
                'message' => 'Successfully connected to cPanel/WHM',
                'data' => [
                    'packages_count' => count($packages),
                ],
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'cPanel connection failed: '.$e->getMessage(),
            ];
        }
    }

    /**
     * Test Plesk connection
     */
    private function testPleskConnection()
    {
        return [
            'success' => true,
            'message' => 'Plesk test pending implementation',
        ];
    }

    /**
     * Test Namecheap connection
     */
    private function testNamecheapConnection()
    {
        return [
            'success' => true,
            'message' => 'Namecheap test pending implementation',
        ];
    }

    /**
     * Test ResellerClub connection
     */
    private function testResellerClubConnection()
    {
        return [
            'success' => true,
            'message' => 'ResellerClub test pending implementation',
        ];
    }

    /**
     * Test SMTP connection
     */
    private function testSMTPConnection()
    {
        try {
            \Mail::raw('Test email from HUBIT', function ($message) {
                $message->to('test@example.com')->subject('HUBIT Connection Test');
            });

            return [
                'success' => true,
                'message' => 'SMTP configured correctly',
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'SMTP test failed: '.$e->getMessage(),
            ];
        }
    }

    /**
     * Extract section from setting key
     */
    private function extractSection($key)
    {
        $parts = explode('.', $key);

        return $parts[0] ?? 'general';
    }

    /**
     * Log setting change for audit
     */
    private function logSettingChange($setting, $oldValue, $newValue, $userId)
    {
        if ($setting->is_secret) {
            $oldValue = $oldValue ? '***REDACTED***' : null;
            $newValue = $newValue ? '***REDACTED***' : null;
        }

        \App\Models\SettingsAudit::create([
            'setting_type' => 'system',
            'setting_id' => $setting->id,
            'key' => $setting->key,
            'old_value' => $oldValue,
            'new_value' => $newValue,
            'changed_by' => $userId,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    /**
     * Clear all settings cache
     */
    public function clearCache()
    {
        Cache::flush();
    }

    /**
     * Get audit logs
     */
    public function getAuditLogs($limit = 100)
    {
        return \App\Models\SettingsAudit::with('changedBy')
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get audit logs for specific keys
     */
    public function getAuditLogsForKeys(array $keys, $limit = 50)
    {
        return \App\Models\SettingsAudit::with('changedBy')
            ->whereIn('key', $keys)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }
}
