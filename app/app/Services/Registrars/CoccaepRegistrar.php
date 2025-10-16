<?php

namespace App\Services\Registrars;

use App\Contracts\RegistrarInterface;
use App\Models\Domain;
use App\Models\DomainOrder;
use App\Services\SettingsService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CoccaepRegistrar implements RegistrarInterface
{
    private SettingsService $settings;

    private string $apiUrl;

    private string $username;

    private string $password;

    private string $registrarCode;

    private array $enabledTlds;

    private array $whoisLanguages;

    public function __construct(SettingsService $settings)
    {
        $this->settings = $settings;
        $this->loadCredentials();
    }

    private function loadCredentials(): void
    {
        $this->apiUrl = $this->settings->get('coccaep.api_base_url', 'https://registry.coccaep.mr/api');
        $this->username = $this->settings->get('coccaep.username', '');
        $this->password = $this->settings->get('coccaep.password', '');
        $this->registrarCode = $this->settings->get('coccaep.registrar_code', '');

        $this->enabledTlds = json_decode(
            $this->settings->get('coccaep.enabled_tlds', '[".mr"]'),
            true
        ) ?? ['.mr'];

        $this->whoisLanguages = json_decode(
            $this->settings->get('coccaep.whois_languages', '["en","ar"]'),
            true
        ) ?? ['en', 'ar'];
    }

    private function makeRequest(string $method, string $endpoint, array $data = []): array
    {
        try {
            $startTime = microtime(true);

            $response = Http::withBasicAuth($this->username, $this->password)
                ->timeout(10)
                ->retry(3, 1000, function ($exception, $request) {
                    return $exception instanceof \Illuminate\Http\Client\ConnectionException;
                })
                ->accept('application/json')
                ->{strtolower($method)}("{$this->apiUrl}/{$endpoint}", array_merge($data, [
                    'registrar_code' => $this->registrarCode,
                ]));

            $latency = (int) ((microtime(true) - $startTime) * 1000);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'data' => $response->json(),
                    'latency' => $latency,
                ];
            }

            Log::error('Coccaep API error', [
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
            Log::error('Coccaep API exception', [
                'endpoint' => $endpoint,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Convert domain to punycode if it's IDN
     */
    private function toPunycode(string $domain): string
    {
        if (! mb_check_encoding($domain, 'ASCII')) {
            $parts = explode('.', $domain);
            $encoded = array_map(function ($part) {
                return idn_to_ascii($part, IDNA_DEFAULT, INTL_IDNA_VARIANT_UTS46);
            }, $parts);

            return implode('.', $encoded);
        }

        return $domain;
    }

    /**
     * Convert punycode to UTF-8 domain
     */
    private function fromPunycode(string $domain): string
    {
        if (str_starts_with($domain, 'xn--')) {
            return idn_to_utf8($domain, IDNA_DEFAULT, INTL_IDNA_VARIANT_UTS46) ?: $domain;
        }

        return $domain;
    }

    public function checkAvailability(string $domain): array
    {
        $punycode = $this->toPunycode($domain);

        $result = $this->makeRequest('POST', 'domains/check', [
            'domain' => $punycode,
        ]);

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
            'price' => $data['price'] ?? null,
            'premium' => $data['premium'] ?? false,
            'idn' => $punycode !== $domain,
            'punycode' => $punycode,
            'message' => $data['available'] ? 'Domain is available' : 'Domain is not available',
        ];
    }

    public function register(DomainOrder $order): array
    {
        $punycode = $this->toPunycode($order->domain);

        $data = [
            'domain' => $punycode,
            'years' => $order->years,
            'contacts' => $this->formatContacts($order),
            'nameservers' => $order->nameservers ?? $this->getDefaultNameservers(),
            'tld' => $this->extractTld($punycode),
        ];

        $result = $this->makeRequest('POST', 'domains/register', $data);

        if (! $result['success']) {
            return [
                'success' => false,
                'domain_id' => null,
                'message' => $result['message'] ?? 'Registration failed',
            ];
        }

        return [
            'success' => true,
            'domain_id' => $result['data']['domain_id'] ?? $punycode,
            'expiry_date' => $result['data']['expiry_date'] ?? null,
            'punycode' => $punycode,
            'message' => 'Domain registered successfully',
        ];
    }

    public function renew(Domain $domain, int $years = 1): array
    {
        $punycode = $domain->punycode ?? $this->toPunycode($domain->fqdn);

        $result = $this->makeRequest('POST', "domains/{$punycode}/renew", [
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
            'expiry_date' => $result['data']['expiry_date'] ?? null,
            'message' => "Domain renewed for {$years} year(s)",
        ];
    }

    public function transfer(Domain $domain, string $authCode): array
    {
        $punycode = $domain->punycode ?? $this->toPunycode($domain->fqdn);

        $data = [
            'domain' => $punycode,
            'auth_code' => $authCode,
        ];

        $result = $this->makeRequest('POST', 'domains/transfer', $data);

        if (! $result['success']) {
            return [
                'success' => false,
                'transfer_id' => null,
                'message' => $result['message'] ?? 'Transfer failed',
            ];
        }

        return [
            'success' => true,
            'transfer_id' => $result['data']['transfer_id'] ?? null,
            'message' => 'Transfer initiated successfully',
        ];
    }

    public function setNameservers(Domain $domain, array $nameservers): array
    {
        $punycode = $domain->punycode ?? $this->toPunycode($domain->fqdn);

        $result = $this->makeRequest('PUT', "domains/{$punycode}/nameservers", [
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
        $punycode = $domain->punycode ?? $this->toPunycode($domain->fqdn);

        $result = $this->makeRequest('GET', "domains/{$punycode}/auth-code");

        if (! $result['success']) {
            return [
                'success' => false,
                'auth_code' => null,
                'message' => $result['message'] ?? 'Failed to retrieve auth code',
            ];
        }

        return [
            'success' => true,
            'auth_code' => $result['data']['auth_code'] ?? null,
            'message' => 'Auth code retrieved successfully',
        ];
    }

    public function setLock(Domain $domain, bool $locked): array
    {
        $punycode = $domain->punycode ?? $this->toPunycode($domain->fqdn);

        $result = $this->makeRequest('PUT', "domains/{$punycode}/lock", [
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
        $punycode = $domain->punycode ?? $this->toPunycode($domain->fqdn);

        $result = $this->makeRequest('PUT', "domains/{$punycode}/privacy", [
            'enabled' => $enabled,
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
        $punycode = $domain->punycode ?? $this->toPunycode($domain->fqdn);

        if (! in_array($language, $this->whoisLanguages)) {
            $language = 'en';
        }

        $result = $this->makeRequest('GET', "domains/{$punycode}/whois", [
            'language' => $language,
        ]);

        if (! $result['success']) {
            return [
                'success' => false,
                'whois' => null,
                'message' => $result['message'] ?? 'Failed to retrieve WHOIS data',
            ];
        }

        $data = $result['data'] ?? [];

        return [
            'success' => true,
            'whois' => [
                'registrant' => $data['registrant'] ?? [],
                'admin' => $data['admin'] ?? [],
                'tech' => $data['tech'] ?? [],
                'billing' => $data['billing'] ?? [],
                'nameservers' => $data['nameservers'] ?? [],
                'locked' => $data['locked'] ?? false,
                'privacy' => $data['privacy'] ?? false,
                'created_date' => $data['created_date'] ?? null,
                'expiry_date' => $data['expiry_date'] ?? null,
                'language' => $language,
            ],
            'message' => 'WHOIS data retrieved successfully',
        ];
    }

    public function sync(Domain $domain): array
    {
        $punycode = $domain->punycode ?? $this->toPunycode($domain->fqdn);

        $result = $this->makeRequest('GET', "domains/{$punycode}");

        if (! $result['success']) {
            return [
                'success' => false,
                'data' => null,
                'message' => $result['message'] ?? 'Sync failed',
            ];
        }

        $data = $result['data'] ?? [];

        return [
            'success' => true,
            'data' => [
                'expiry_date' => $data['expiry_date'] ?? null,
                'status' => $data['status'] ?? 'unknown',
                'locked' => $data['locked'] ?? false,
                'privacy' => $data['privacy'] ?? false,
                'nameservers' => $data['nameservers'] ?? [],
                'tld' => $data['tld'] ?? $this->extractTld($punycode),
            ],
            'message' => 'Domain synced successfully',
        ];
    }

    public function testConnection(): array
    {
        $result = $this->makeRequest('GET', 'status');

        if (! $result['success']) {
            return [
                'success' => false,
                'latency' => null,
                'message' => $result['message'] ?? 'Connection test failed',
                'details' => [],
            ];
        }

        $data = $result['data'] ?? [];

        return [
            'success' => true,
            'latency' => $result['latency'] ?? null,
            'message' => 'Connected successfully',
            'details' => [
                'api_version' => $data['version'] ?? 'EPP/XML 1.0',
                'endpoint' => $this->apiUrl,
                'registry_status' => $data['status'] ?? 'operational',
                'enabled_tlds' => $this->enabledTlds,
                'whois_languages' => $this->whoisLanguages,
            ],
        ];
    }

    private function formatContacts(DomainOrder $order): array
    {
        $contact = [
            'first_name' => $order->contact_first_name,
            'last_name' => $order->contact_last_name,
            'email' => $order->contact_email,
            'phone' => $order->contact_phone ?? '',
            'address' => $order->contact_address ?? '',
            'city' => $order->contact_city ?? '',
            'state' => $order->contact_state ?? '',
            'zip' => $order->contact_zip ?? '',
            'country' => $order->contact_country ?? 'MR',
        ];

        return [
            'registrant' => $contact,
            'admin' => $contact,
            'tech' => $contact,
            'billing' => $contact,
        ];
    }

    private function extractTld(string $domain): string
    {
        $parts = explode('.', $domain);

        if (count($parts) >= 3) {
            $possibleTld = '.'.$parts[count($parts) - 2].'.'.$parts[count($parts) - 1];
            if (in_array($possibleTld, $this->enabledTlds)) {
                return $possibleTld;
            }
        }

        return '.'.end($parts);
    }

    private function getDefaultNameservers(): array
    {
        return [
            'ns1.coccaep.mr',
            'ns2.coccaep.mr',
        ];
    }
}
