<?php

namespace App\Services\Testing\Mocks;

class MockProvisionerClient
{
    private string $provider;

    public function __construct(string $provider = 'mock')
    {
        $this->provider = $provider;
    }

    public function createAccount(array $params): array
    {
        usleep(800000); // 800ms realistic provision time
        
        $username = $params['username'] ?? 'mockuser' . rand(1000, 9999);
        $domain = $params['domain'] ?? 'mock' . rand(1000, 9999) . '.example.com';
        
        return [
            'success' => true,
            'username' => $username,
            'domain' => $domain,
            'password' => 'MockPass!' . rand(10000, 99999),
            'ip_address' => '192.0.2.' . rand(1, 254),
            'nameservers' => [
                'ns1.mock-hosting.com',
                'ns2.mock-hosting.com',
            ],
            'package' => $params['package'] ?? 'MOCK_BASIC',
            'provider' => $this->provider,
            'panel_url' => 'https://mock-panel.example.com:2083',
        ];
    }

    public function suspendAccount(string $username): array
    {
        usleep(300000);
        
        return [
            'success' => true,
            'username' => $username,
            'status' => 'suspended',
            'reason' => 'Administrative action',
        ];
    }

    public function unsuspendAccount(string $username): array
    {
        usleep(300000);
        
        return [
            'success' => true,
            'username' => $username,
            'status' => 'active',
        ];
    }

    public function terminateAccount(string $username): array
    {
        usleep(500000);
        
        return [
            'success' => true,
            'username' => $username,
            'status' => 'terminated',
        ];
    }

    public function changePackage(string $username, string $package): array
    {
        usleep(400000);
        
        return [
            'success' => true,
            'username' => $username,
            'old_package' => 'MOCK_BASIC',
            'new_package' => $package,
        ];
    }

    public function resetPassword(string $username, string $password): array
    {
        usleep(200000);
        
        return [
            'success' => true,
            'username' => $username,
            'password' => $password,
        ];
    }

    public function getUsage(string $username): array
    {
        return [
            'success' => true,
            'username' => $username,
            'disk_used' => rand(100, 5000),
            'disk_limit' => 10000,
            'bandwidth_used' => rand(500, 50000),
            'bandwidth_limit' => 100000,
            'inodes_used' => rand(1000, 50000),
        ];
    }

    public function listPackages(): array
    {
        return [
            ['name' => 'MOCK_BASIC', 'disk' => 10000, 'bandwidth' => 100000],
            ['name' => 'MOCK_PRO', 'disk' => 50000, 'bandwidth' => 500000],
            ['name' => 'MOCK_BUSINESS', 'disk' => 100000, 'bandwidth' => -1],
        ];
    }
}
