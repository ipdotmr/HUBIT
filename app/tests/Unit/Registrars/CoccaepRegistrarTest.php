<?php

namespace Tests\Unit\Registrars;

use App\Models\Domain;
use App\Models\DomainOrder;
use App\Services\Registrars\CoccaepRegistrar;
use App\Services\SettingsService;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class CoccaepRegistrarTest extends TestCase
{
    private CoccaepRegistrar $registrar;

    private SettingsService $settings;

    protected function setUp(): void
    {
        parent::setUp();

        $this->settings = $this->createMock(SettingsService::class);
        $this->settings->method('get')->willReturnMap([
            ['coccaep.api_base_url', 'https://registry.coccaep.mr/api', 'https://registry.coccaep.mr/api'],
            ['coccaep.username', '', 'testuser'],
            ['coccaep.password', '', 'testpass'],
            ['coccaep.registrar_code', '', 'MR-TEST'],
            ['coccaep.enabled_tlds', '[".mr"]', '[".mr",".gov.mr",".edu.mr",".xn--mgbah1a"]'],
            ['coccaep.whois_languages', '["en","ar"]', '["en","ar","fr"]'],
        ]);

        $this->registrar = new CoccaepRegistrar($this->settings);
    }

    /** @test */
    public function it_checks_domain_availability_for_regular_mr_domain()
    {
        Http::fake([
            '*/domains/check' => Http::response([
                'available' => true,
                'price' => 50.00,
                'premium' => false,
            ], 200),
        ]);

        $result = $this->registrar->checkAvailability('example.mr');

        $this->assertTrue($result['available']);
        $this->assertEquals(50.00, $result['price']);
        $this->assertFalse($result['idn']);
    }

    /** @test */
    public function it_handles_idn_domain_availability_check()
    {
        Http::fake([
            '*/domains/check' => Http::response([
                'available' => true,
                'price' => 50.00,
            ], 200),
        ]);

        $result = $this->registrar->checkAvailability('موريتانيا.mr');

        $this->assertTrue($result['available']);
        $this->assertTrue($result['idn']);
        $this->assertStringContainsString('xn--', $result['punycode']);
    }

    /** @test */
    public function it_checks_multilevel_tld_availability()
    {
        Http::fake([
            '*/domains/check' => Http::response([
                'available' => true,
                'price' => 0.00, // Government domains may be free
            ], 200),
        ]);

        $result = $this->registrar->checkAvailability('ministry.gov.mr');

        $this->assertTrue($result['available']);
    }

    /** @test */
    public function it_registers_regular_domain()
    {
        Http::fake([
            '*/domains/register' => Http::response([
                'domain_id' => 'example.mr',
                'expiry_date' => '2025-10-15T00:00:00Z',
            ], 200),
        ]);

        $order = $this->createMockDomainOrder([
            'domain' => 'example.mr',
            'years' => 1,
            'contact_first_name' => 'Ahmed',
            'contact_last_name' => 'Hassan',
            'contact_email' => 'ahmed@example.mr',
            'contact_country' => 'MR',
        ]);

        $result = $this->registrar->register($order);

        $this->assertTrue($result['success']);
        $this->assertEquals('example.mr', $result['domain_id']);
    }

    /** @test */
    public function it_registers_idn_domain_with_punycode_conversion()
    {
        Http::fake([
            '*/domains/register' => Http::response([
                'domain_id' => 'xn--mgbah1a.mr',
                'expiry_date' => '2025-10-15T00:00:00Z',
            ], 200),
        ]);

        $order = $this->createMockDomainOrder([
            'domain' => 'موريتانيا.mr',
            'years' => 1,
            'contact_first_name' => 'Ahmed',
            'contact_last_name' => 'Hassan',
            'contact_email' => 'ahmed@example.mr',
            'contact_country' => 'MR',
        ]);

        $result = $this->registrar->register($order);

        $this->assertTrue($result['success']);
        $this->assertStringContainsString('xn--', $result['punycode']);
    }

    /** @test */
    public function it_renews_domain()
    {
        Http::fake([
            '*/domains/*/renew' => Http::response([
                'expiry_date' => '2026-10-15T00:00:00Z',
            ], 200),
        ]);

        $domain = new Domain([
            'fqdn' => 'example.mr',
            'punycode' => 'example.mr',
        ]);

        $result = $this->registrar->renew($domain, 1);

        $this->assertTrue($result['success']);
        $this->assertNotNull($result['expiry_date']);
    }

    /** @test */
    public function it_sets_nameservers()
    {
        Http::fake([
            '*/domains/*/nameservers' => Http::response([], 200),
        ]);

        $domain = new Domain([
            'fqdn' => 'example.mr',
            'punycode' => 'example.mr',
        ]);

        $nameservers = ['ns1.coccaep.mr', 'ns2.coccaep.mr'];

        $result = $this->registrar->setNameservers($domain, $nameservers);

        $this->assertTrue($result['success']);
    }

    /** @test */
    public function it_gets_auth_code()
    {
        Http::fake([
            '*domains/example.mr/auth-code*' => Http::response([
                'auth_code' => 'COCCAEP-AUTH-123',
            ], 200),
        ]);

        $domain = new Domain([
            'fqdn' => 'example.mr',
            'punycode' => 'example.mr',
        ]);

        $result = $this->registrar->getAuthCode($domain);

        $this->assertTrue($result['success']);
        $this->assertEquals('COCCAEP-AUTH-123', $result['auth_code']);
    }

    /** @test */
    public function it_toggles_domain_lock()
    {
        Http::fake([
            '*/domains/*/lock' => Http::response([], 200),
        ]);

        $domain = new Domain([
            'fqdn' => 'example.mr',
            'punycode' => 'example.mr',
        ]);

        $lockResult = $this->registrar->setLock($domain, true);
        $this->assertTrue($lockResult['success']);
        $this->assertStringContainsString('locked', strtolower($lockResult['message']));

        $unlockResult = $this->registrar->setLock($domain, false);
        $this->assertTrue($unlockResult['success']);
        $this->assertStringContainsString('unlocked', strtolower($unlockResult['message']));
    }

    /** @test */
    public function it_toggles_privacy()
    {
        Http::fake([
            '*/domains/*/privacy' => Http::response([], 200),
        ]);

        $domain = new Domain([
            'fqdn' => 'example.mr',
            'punycode' => 'example.mr',
        ]);

        $enableResult = $this->registrar->setPrivacy($domain, true);
        $this->assertTrue($enableResult['success']);

        $disableResult = $this->registrar->setPrivacy($domain, false);
        $this->assertTrue($disableResult['success']);
    }

    /** @test */
    public function it_fetches_whois_in_english()
    {
        Http::fake([
            '*/domains/*/whois*' => Http::response([
                'registrant' => ['name' => 'Ahmed Hassan', 'email' => 'ahmed@example.mr'],
                'admin' => ['name' => 'Admin Contact', 'email' => 'admin@example.mr'],
                'tech' => ['name' => 'Tech Contact', 'email' => 'tech@example.mr'],
                'nameservers' => ['ns1.coccaep.mr', 'ns2.coccaep.mr'],
                'locked' => true,
                'privacy' => false,
                'created_date' => '2020-01-01T00:00:00Z',
                'expiry_date' => '2025-01-01T00:00:00Z',
            ], 200),
        ]);

        $domain = new Domain([
            'fqdn' => 'example.mr',
            'punycode' => 'example.mr',
        ]);

        $result = $this->registrar->getWhois($domain, 'en');

        $this->assertTrue($result['success']);
        $this->assertEquals('en', $result['whois']['language']);
        $this->assertArrayHasKey('registrant', $result['whois']);
    }

    /** @test */
    public function it_fetches_whois_in_arabic()
    {
        Http::fake([
            '*/domains/*/whois*' => Http::response([
                'registrant' => ['name' => 'أحمد حسن', 'email' => 'ahmed@example.mr'],
                'locked' => false,
            ], 200),
        ]);

        $domain = new Domain([
            'fqdn' => 'example.mr',
            'punycode' => 'example.mr',
        ]);

        $result = $this->registrar->getWhois($domain, 'ar');

        $this->assertTrue($result['success']);
        $this->assertEquals('ar', $result['whois']['language']);
    }

    /** @test */
    public function it_fetches_whois_in_french()
    {
        Http::fake([
            '*/domains/*/whois*' => Http::response([
                'registrant' => ['name' => 'Ahmed Hassan', 'email' => 'ahmed@example.mr'],
            ], 200),
        ]);

        $domain = new Domain([
            'fqdn' => 'example.mr',
            'punycode' => 'example.mr',
        ]);

        $result = $this->registrar->getWhois($domain, 'fr');

        $this->assertTrue($result['success']);
        $this->assertEquals('fr', $result['whois']['language']);
    }

    /** @test */
    public function it_falls_back_to_english_for_unsupported_language()
    {
        Http::fake([
            '*/domains/*/whois*' => Http::response([
                'registrant' => ['name' => 'Ahmed Hassan'],
            ], 200),
        ]);

        $domain = new Domain([
            'fqdn' => 'example.mr',
            'punycode' => 'example.mr',
        ]);

        $result = $this->registrar->getWhois($domain, 'de'); // German not supported

        $this->assertTrue($result['success']);
        $this->assertEquals('en', $result['whois']['language']);
    }

    /** @test */
    public function it_syncs_domain_data()
    {
        Http::fake([
            '*/domains/*' => Http::response([
                'expiry_date' => '2025-10-15T00:00:00Z',
                'status' => 'active',
                'locked' => false,
                'privacy' => true,
                'nameservers' => ['ns1.coccaep.mr', 'ns2.coccaep.mr'],
                'tld' => '.mr',
            ], 200),
        ]);

        $domain = new Domain([
            'fqdn' => 'example.mr',
            'punycode' => 'example.mr',
        ]);

        $result = $this->registrar->sync($domain);

        $this->assertTrue($result['success']);
        $this->assertEquals('active', $result['data']['status']);
        $this->assertFalse($result['data']['locked']);
        $this->assertTrue($result['data']['privacy']);
        $this->assertEquals('.mr', $result['data']['tld']);
    }

    /** @test */
    public function it_tests_connection_successfully()
    {
        Http::fake([
            '*status*' => Http::response([
                'version' => 'EPP/XML 1.0',
                'status' => 'operational',
            ], 200),
        ]);

        $result = $this->registrar->testConnection();

        $this->assertTrue($result['success']);
        $this->assertNotNull($result['latency']);
        $this->assertEquals('EPP/XML 1.0', $result['details']['api_version']);
        $this->assertEquals('operational', $result['details']['registry_status']);
        $this->assertArrayHasKey('enabled_tlds', $result['details']);
        $this->assertArrayHasKey('whois_languages', $result['details']);
    }

    /** @test */
    public function it_handles_connection_failure()
    {
        Http::fake([
            '*/status' => Http::response(['message' => 'Unauthorized'], 401),
        ]);

        $result = $this->registrar->testConnection();

        $this->assertFalse($result['success']);
    }

    /** @test */
    public function it_retries_on_connection_timeout()
    {
        $attempts = 0;
        Http::fake(function () use (&$attempts) {
            $attempts++;
            if ($attempts < 3) {
                throw new \Illuminate\Http\Client\ConnectionException('Connection timeout');
            }

            return Http::response(['available' => true], 200);
        });

        $result = $this->registrar->checkAvailability('example.mr');

        $this->assertEquals(3, $attempts);
        $this->assertTrue($result['available']);
    }

    private function createMockDomain(string $name, ?string $punycode = null): Domain
    {
        return new Domain([
            'name' => $name,
            'fqdn' => $name,
            'punycode' => $punycode ?? $name,
        ]);
    }

    private function createMockDomainOrder(array $data): DomainOrder
    {
        $defaults = [
            'domain' => 'example.mr',
            'years' => 1,
            'contact_first_name' => 'Ahmed',
            'contact_last_name' => 'Hassan',
            'contact_email' => 'ahmed@example.mr',
            'contact_country' => 'MR',
        ];

        return new DomainOrder(array_merge($defaults, $data));
    }
}
