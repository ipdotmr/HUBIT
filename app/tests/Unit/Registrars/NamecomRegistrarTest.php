<?php

namespace Tests\Unit\Registrars;

use App\Models\Domain;
use App\Models\DomainOrder;
use App\Services\Registrars\NamecomRegistrar;
use App\Services\SettingsService;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class NamecomRegistrarTest extends TestCase
{
    private NamecomRegistrar $registrar;

    private SettingsService $settings;

    protected function setUp(): void
    {
        parent::setUp();

        $this->settings = $this->createMock(SettingsService::class);
        $this->settings->method('get')->willReturnMap([
            ['namecom.mode', 'test', 'test'],
            ['namecom.sandbox_url', null, 'https://api.dev.name.com/v4'],
            ['namecom.api_username', '', 'testuser'],
            ['namecom.api_token', '', 'testtoken123'],
        ]);

        $this->registrar = new NamecomRegistrar($this->settings);
    }

    /** @test */
    public function it_checks_domain_availability_successfully()
    {
        Http::fake([
            '*/domains:checkAvailability*' => Http::response([
                'available' => true,
                'purchasePrice' => 12.99,
                'premium' => false,
            ], 200),
        ]);

        $result = $this->registrar->checkAvailability('example.com');

        $this->assertTrue($result['available']);
        $this->assertEquals(12.99, $result['price']);
        $this->assertFalse($result['premium']);
    }

    private function createMockDomain(string $name): Domain
    {
        return new Domain([
            'name' => $name,
            'fqdn' => $name,
        ]);
    }

    private function createMockDomainOrder(array $data): DomainOrder
    {
        $defaults = [
            'domain' => 'example.com',
            'price' => 12.99,
            'years' => 1,
            'contact_first_name' => 'John',
            'contact_last_name' => 'Doe',
            'contact_email' => 'john@example.com',
        ];

        return new DomainOrder(array_merge($defaults, $data));
    }

    /** @test */
    public function it_handles_unavailable_domain()
    {
        Http::fake([
            '*/domains:checkAvailability*' => Http::response([
                'available' => false,
            ], 200),
        ]);

        $result = $this->registrar->checkAvailability('taken.com');

        $this->assertFalse($result['available']);
        $this->assertStringContainsString('not available', $result['message']);
    }

    /** @test */
    public function it_registers_domain_successfully()
    {
        Http::fake([
            '*/domains' => Http::response([
                'domainName' => 'newdomain.com',
                'expireDate' => '2025-10-15T00:00:00Z',
            ], 200),
        ]);

        $order = $this->createMockDomainOrder(['domain' => 'newdomain.com']);

        $result = $this->registrar->register($order);

        $this->assertTrue($result['success']);
        $this->assertEquals('newdomain.com', $result['domain_id']);
        $this->assertNotNull($result['expiry_date']);
    }

    /** @test */
    public function it_handles_registration_failure()
    {
        Http::fake([
            '*/domains' => Http::response([
                'message' => 'Domain already registered',
            ], 400),
        ]);

        $order = $this->createMockDomainOrder(['domain' => 'taken.com']);

        $result = $this->registrar->register($order);

        $this->assertFalse($result['success']);
        $this->assertNull($result['domain_id']);
    }

    /** @test */
    public function it_renews_domain_successfully()
    {
        Http::fake([
            '*/domains/*/renew' => Http::response([
                'expireDate' => '2026-10-15T00:00:00Z',
            ], 200),
        ]);

        $domain = $this->createMockDomain('example.com');

        $result = $this->registrar->renew($domain, 1);

        $this->assertTrue($result['success']);
        $this->assertNotNull($result['expiry_date']);
        $this->assertStringContainsString('renewed', strtolower($result['message']));
    }

    /** @test */
    public function it_sets_nameservers_successfully()
    {
        Http::fake([
            '*/domains/*/setNameservers' => Http::response([], 200),
        ]);

        $domain = $this->createMockDomain('example.com');
        $nameservers = ['ns1.example.com', 'ns2.example.com'];

        $result = $this->registrar->setNameservers($domain, $nameservers);

        $this->assertTrue($result['success']);
        $this->assertStringContainsString('updated', strtolower($result['message']));
    }

    /** @test */
    public function it_gets_auth_code_successfully()
    {
        Http::fake([
            '*/domains/*/getAuthCode' => Http::response([
                'authCode' => 'ABC123XYZ',
            ], 200),
        ]);

        $domain = $this->createMockDomain('example.com');

        $result = $this->registrar->getAuthCode($domain);

        $this->assertTrue($result['success']);
        $this->assertEquals('ABC123XYZ', $result['auth_code']);
    }

    /** @test */
    public function it_toggles_domain_lock()
    {
        Http::fake([
            '*/domains/*/setLocked' => Http::response([], 200),
        ]);

        $domain = $this->createMockDomain('example.com');

        $lockResult = $this->registrar->setLock($domain, true);
        $this->assertTrue($lockResult['success']);
        $this->assertStringContainsString('locked', strtolower($lockResult['message']));

        $unlockResult = $this->registrar->setLock($domain, false);
        $this->assertTrue($unlockResult['success']);
        $this->assertStringContainsString('unlocked', strtolower($unlockResult['message']));
    }

    /** @test */
    public function it_toggles_privacy_protection()
    {
        Http::fake([
            '*/domains/*/setPrivacy' => Http::response([], 200),
        ]);

        $domain = $this->createMockDomain('example.com');

        $enableResult = $this->registrar->setPrivacy($domain, true);
        $this->assertTrue($enableResult['success']);
        $this->assertStringContainsString('enabled', strtolower($enableResult['message']));

        $disableResult = $this->registrar->setPrivacy($domain, false);
        $this->assertTrue($disableResult['success']);
        $this->assertStringContainsString('disabled', strtolower($disableResult['message']));
    }

    /** @test */
    public function it_fetches_whois_data()
    {
        Http::fake([
            '*/domains/*' => Http::response([
                'contacts' => [
                    'registrant' => ['name' => 'John Doe', 'email' => 'john@example.com'],
                    'admin' => ['name' => 'Jane Doe', 'email' => 'jane@example.com'],
                ],
                'nameservers' => ['ns1.example.com', 'ns2.example.com'],
                'locked' => true,
                'privacyEnabled' => false,
                'createDate' => '2020-01-01T00:00:00Z',
                'expireDate' => '2025-01-01T00:00:00Z',
            ], 200),
        ]);

        $domain = $this->createMockDomain('example.com');

        $result = $this->registrar->getWhois($domain);

        $this->assertTrue($result['success']);
        $this->assertArrayHasKey('registrant', $result['whois']);
        $this->assertArrayHasKey('nameservers', $result['whois']);
        $this->assertTrue($result['whois']['locked']);
        $this->assertFalse($result['whois']['privacy']);
    }

    /** @test */
    public function it_syncs_domain_data()
    {
        Http::fake([
            '*/domains/*' => Http::response([
                'expireDate' => '2025-10-15T00:00:00Z',
                'status' => 'active',
                'locked' => false,
                'privacyEnabled' => true,
                'nameservers' => ['ns1.example.com', 'ns2.example.com'],
                'autorenewEnabled' => true,
            ], 200),
        ]);

        $domain = $this->createMockDomain('example.com');

        $result = $this->registrar->sync($domain);

        $this->assertTrue($result['success']);
        $this->assertEquals('active', $result['data']['status']);
        $this->assertFalse($result['data']['locked']);
        $this->assertTrue($result['data']['privacy']);
        $this->assertTrue($result['data']['auto_renew']);
    }

    /** @test */
    public function it_tests_connection_successfully()
    {
        Http::fake([
            '*/domains' => Http::response([
                'domains' => [
                    ['domainName' => 'example1.com'],
                    ['domainName' => 'example2.com'],
                ],
            ], 200),
        ]);

        $result = $this->registrar->testConnection();

        $this->assertTrue($result['success']);
        $this->assertNotNull($result['latency']);
        $this->assertEquals(2, $result['details']['domain_count']);
        $this->assertEquals('v4', $result['details']['api_version']);
    }

    /** @test */
    public function it_handles_connection_failure()
    {
        Http::fake([
            '*/domains' => Http::response(['message' => 'Unauthorized'], 401),
        ]);

        $result = $this->registrar->testConnection();

        $this->assertFalse($result['success']);
        $this->assertNull($result['latency']);
    }

    /** @test */
    public function it_handles_network_timeout()
    {
        Http::fake(function () {
            throw new \Illuminate\Http\Client\ConnectionException('Connection timeout');
        });

        $result = $this->registrar->checkAvailability('example.com');

        $this->assertFalse($result['available']);
        $this->assertStringContainsString('timeout', strtolower($result['message']));
    }
}
