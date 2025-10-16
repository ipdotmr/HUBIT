<?php

namespace App\Services\Testing\Mocks;

class MockRegistrarClient
{
    private string $registrar;

    public function __construct(string $registrar = 'mock')
    {
        $this->registrar = $registrar;
    }

    public function checkAvailability(string $domain): array
    {
        usleep(200000); // 200ms realistic DNS lookup
        
        $available = ! in_array($domain, ['google.com', 'facebook.com', 'apple.com']);
        
        return [
            'domain' => $domain,
            'available' => $available,
            'price' => $available ? 15.00 : null,
            'currency' => 'USD',
            'registrar' => $this->registrar,
        ];
    }

    public function register(string $domain, array $contact, int $years = 1): array
    {
        usleep(500000); // 500ms realistic registration time
        
        return [
            'success' => true,
            'domain' => $domain,
            'order_id' => 'REG-MOCK-' . strtoupper(uniqid()),
            'status' => 'active',
            'expires_at' => now()->addYears($years)->toDateTimeString(),
            'nameservers' => ['ns1.mock-registrar.com', 'ns2.mock-registrar.com'],
            'registrar' => $this->registrar,
        ];
    }

    public function renew(string $domain, int $years = 1): array
    {
        usleep(300000);
        
        return [
            'success' => true,
            'domain' => $domain,
            'expires_at' => now()->addYears($years)->toDateTimeString(),
            'order_id' => 'RENEW-MOCK-' . uniqid(),
        ];
    }

    public function transfer(string $domain, string $authCode): array
    {
        usleep(600000);
        
        return [
            'success' => true,
            'domain' => $domain,
            'status' => 'pending',
            'transfer_id' => 'TRANSFER-MOCK-' . uniqid(),
        ];
    }

    public function getEppCode(string $domain): array
    {
        return [
            'success' => true,
            'domain' => $domain,
            'epp_code' => 'EPP-MOCK-' . strtoupper(substr(md5($domain), 0, 16)),
        ];
    }

    public function updateNameservers(string $domain, array $nameservers): array
    {
        usleep(250000);
        
        return [
            'success' => true,
            'domain' => $domain,
            'nameservers' => $nameservers,
        ];
    }

    public function setLock(string $domain, bool $locked): array
    {
        return [
            'success' => true,
            'domain' => $domain,
            'locked' => $locked,
        ];
    }

    public function setPrivacy(string $domain, bool $enabled): array
    {
        return [
            'success' => true,
            'domain' => $domain,
            'privacy' => $enabled,
        ];
    }
}
