<?php

namespace App\Services\Registrars;

use App\Contracts\RegistrarInterface;
use App\Models\Domain;
use App\Models\DomainOrder;
use App\Services\SettingsService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class NamecomRegistrar implements RegistrarInterface
{
    private SettingsService $settings;

    private string $apiUrl;

    private string $username;

    private string $token;

    public function __construct(SettingsService $settings)
    {
        $this->settings = $settings;
        $this->loadCredentials();
    }

    private function loadCredentials(): void
    {
        $mode = $this->settings->get('namecom.mode', 'test');
        $sandboxUrl = $this->settings->get('namecom.sandbox_url');

        $this->apiUrl = $mode === 'live'
            ? 'https://api.name.com/v4'
            : ($sandboxUrl ?: 'https://api.dev.name.com/v4');

        $this->username = $this->settings->get('namecom.api_username', '');
        $this->token = $this->settings->get('namecom.api_token', '');
    }

    private function makeRequest(string $method, string $endpoint, array $data = []): array
    {
        try {
            $startTime = microtime(true);

            $response = Http::withBasicAuth($this->username, $this->token)
                ->timeout(30)
                ->accept('application/json')
                ->{strtolower($method)}("{$this->apiUrl}/{$endpoint}", $data);

            $latency = (int) ((microtime(true) - $startTime) * 1000);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'data' => $response->json(),
                    'latency' => $latency,
                ];
            }

            Log::error('Name.com API error', [
                'endpoint' => $endpoint,
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return [
                'success' => false,
                'message' => $response->json('message') ?? 'API request failed',
                'error' => $response->json(),
            ];
        } catch (\Exception $e) {
            Log::error('Name.com API exception', [
                'endpoint' => $endpoint,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    public function checkAvailability(string $domain): array
    {
        $result = $this->makeRequest('GET', "domains:checkAvailability?domainName={$domain}");

        if (! $result['success']) {
            return [
                'available' => false,
                'price' => null,
                'premium' => false,
                'message' => $result['message'] ?? 'Failed to check availability',
            ];
        }

        $data = $result['data'] ?? [];

        return [
            'available' => $data['available'] ?? false,
            'price' => $data['purchasePrice'] ?? null,
            'premium' => $data['premium'] ?? false,
            'message' => $data['available'] ? 'Domain is available' : 'Domain is not available',
        ];
    }

    public function register(DomainOrder $order): array
    {
        $data = [
            'domain' => [
                'domainName' => $order->domain,
            ],
            'purchasePrice' => $order->price,
            'years' => $order->years,
            'contacts' => $this->formatContacts($order),
            'nameservers' => $order->nameservers ?? [],
            'privacyEnabled' => $order->privacy ?? false,
        ];

        $result = $this->makeRequest('POST', 'domains', $data);

        if (! $result['success']) {
            return [
                'success' => false,
                'domain_id' => null,
                'message' => $result['message'] ?? 'Registration failed',
            ];
        }

        return [
            'success' => true,
            'domain_id' => $result['data']['domainName'] ?? null,
            'expiry_date' => $result['data']['expireDate'] ?? null,
            'message' => 'Domain registered successfully',
        ];
    }

    public function renew(Domain $domain, int $years = 1): array
    {
        $result = $this->makeRequest('POST', "domains/{$domain->name}:renew", [
            'years' => $years,
        ]);

        if (! $result['success']) {
            return [
                'success' => false,
                'expiry_date' => null,
                'message' => $result['message'] ?? 'Renewal failed',
            ];
        }

        return [
            'success' => true,
            'expiry_date' => $result['data']['expireDate'] ?? null,
            'message' => "Domain renewed for {$years} year(s)",
        ];
    }

    public function transfer(Domain $domain, string $authCode): array
    {
        $data = [
            'domainName' => $domain->name,
            'authCode' => $authCode,
        ];

        $result = $this->makeRequest('POST', 'domains:transfer', $data);

        if (! $result['success']) {
            return [
                'success' => false,
                'transfer_id' => null,
                'message' => $result['message'] ?? 'Transfer failed',
            ];
        }

        return [
            'success' => true,
            'transfer_id' => $result['data']['orderNumber'] ?? null,
            'message' => 'Transfer initiated successfully',
        ];
    }

    public function setNameservers(Domain $domain, array $nameservers): array
    {
        $result = $this->makeRequest('PUT', "domains/{$domain->name}:setNameservers", [
            'nameservers' => $nameservers,
        ]);

        if (! $result['success']) {
            return [
                'success' => false,
                'message' => $result['message'] ?? 'Failed to update nameservers',
            ];
        }

        return [
            'success' => true,
            'message' => 'Nameservers updated successfully',
        ];
    }

    public function getAuthCode(Domain $domain): array
    {
        $result = $this->makeRequest('GET', "domains/{$domain->name}:getAuthCode");

        if (! $result['success']) {
            return [
                'success' => false,
                'auth_code' => null,
                'message' => $result['message'] ?? 'Failed to retrieve auth code',
            ];
        }

        return [
            'success' => true,
            'auth_code' => $result['data']['authCode'] ?? null,
            'message' => 'Auth code retrieved successfully',
        ];
    }

    public function setLock(Domain $domain, bool $locked): array
    {
        $result = $this->makeRequest('PUT', "domains/{$domain->name}:setLocked", [
            'locked' => $locked,
        ]);

        if (! $result['success']) {
            return [
                'success' => false,
                'message' => $result['message'] ?? 'Failed to update lock status',
            ];
        }

        return [
            'success' => true,
            'message' => $locked ? 'Domain locked successfully' : 'Domain unlocked successfully',
        ];
    }

    public function setPrivacy(Domain $domain, bool $enabled): array
    {
        $result = $this->makeRequest('PUT', "domains/{$domain->name}:setPrivacy", [
            'privacy' => $enabled,
        ]);

        if (! $result['success']) {
            return [
                'success' => false,
                'message' => $result['message'] ?? 'Failed to update privacy settings',
            ];
        }

        return [
            'success' => true,
            'message' => $enabled ? 'Privacy protection enabled' : 'Privacy protection disabled',
        ];
    }

    public function getWhois(Domain $domain, string $language = 'en'): array
    {
        $result = $this->makeRequest('GET', "domains/{$domain->name}");

        if (! $result['success']) {
            return [
                'success' => false,
                'whois' => null,
                'message' => $result['message'] ?? 'Failed to retrieve WHOIS data',
            ];
        }

        $domainData = $result['data'] ?? [];

        return [
            'success' => true,
            'whois' => [
                'registrant' => $domainData['contacts']['registrant'] ?? [],
                'admin' => $domainData['contacts']['admin'] ?? [],
                'tech' => $domainData['contacts']['tech'] ?? [],
                'billing' => $domainData['contacts']['billing'] ?? [],
                'nameservers' => $domainData['nameservers'] ?? [],
                'locked' => $domainData['locked'] ?? false,
                'privacy' => $domainData['privacyEnabled'] ?? false,
                'created_date' => $domainData['createDate'] ?? null,
                'expiry_date' => $domainData['expireDate'] ?? null,
            ],
            'message' => 'WHOIS data retrieved successfully',
        ];
    }

    public function sync(Domain $domain): array
    {
        $result = $this->makeRequest('GET', "domains/{$domain->name}");

        if (! $result['success']) {
            return [
                'success' => false,
                'data' => null,
                'message' => $result['message'] ?? 'Sync failed',
            ];
        }

        $domainData = $result['data'] ?? [];

        return [
            'success' => true,
            'data' => [
                'expiry_date' => $domainData['expireDate'] ?? null,
                'status' => $domainData['status'] ?? 'unknown',
                'locked' => $domainData['locked'] ?? false,
                'privacy' => $domainData['privacyEnabled'] ?? false,
                'nameservers' => $domainData['nameservers'] ?? [],
                'auto_renew' => $domainData['autorenewEnabled'] ?? false,
            ],
            'message' => 'Domain synced successfully',
        ];
    }

    public function testConnection(): array
    {
        $result = $this->makeRequest('GET', 'domains');

        if (! $result['success']) {
            return [
                'success' => false,
                'latency' => null,
                'message' => $result['message'] ?? 'Connection test failed',
                'details' => [],
            ];
        }

        $domains = $result['data']['domains'] ?? [];

        return [
            'success' => true,
            'latency' => $result['latency'] ?? null,
            'message' => 'Connected successfully',
            'details' => [
                'api_version' => 'v4',
                'endpoint' => $this->apiUrl,
                'domain_count' => count($domains),
                'status' => 'operational',
            ],
        ];
    }

    private function formatContacts(DomainOrder $order): array
    {
        $contact = [
            'firstName' => $order->contact_first_name,
            'lastName' => $order->contact_last_name,
            'email' => $order->contact_email,
            'phone' => $order->contact_phone ?? '',
            'address1' => $order->contact_address ?? '',
            'city' => $order->contact_city ?? '',
            'state' => $order->contact_state ?? '',
            'zip' => $order->contact_zip ?? '',
            'country' => $order->contact_country ?? '',
        ];

        return [
            'registrant' => $contact,
            'admin' => $contact,
            'tech' => $contact,
            'billing' => $contact,
        ];
    }
}
