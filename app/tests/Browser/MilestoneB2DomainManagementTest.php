<?php

namespace Tests\Browser;

use App\Models\Client;
use App\Models\Domain;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class MilestoneB2DomainManagementTest extends DuskTestCase
{
    use DatabaseMigrations;

    protected function setUp(): void
    {
        parent::setUp();

        config(['testing.use_mocks' => true]);
    }

    public function test_client_can_view_domains_list_with_filters(): void
    {
        $user = User::factory()->create();
        $client = Client::factory()->create(['user_id' => $user->id]);

        Domain::factory()->count(5)->create([
            'client_id' => $client->id,
            'status' => 'active',
            'registrar' => 'namecheap',
        ]);

        Domain::factory()->create([
            'client_id' => $client->id,
            'status' => 'pending',
            'registrar' => 'namecom',
        ]);

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                ->visit('/client/domains')
                ->waitForText('My Domains')
                ->assertSee('example.com')
                ->assertSeeIn('table', 'Active')

                ->select('[name="registrar"]', 'namecheap')
                ->press('Apply')
                ->waitForReload()
                ->assertSee('namecheap')
                ->assertDontSee('namecom')

                ->select('[name="status"]', 'pending')
                ->press('Apply')
                ->waitForReload()
                ->assertSee('Pending')

                ->press('Clear')
                ->waitForReload()
                ->assertSee('All')
                ->screenshot('domains-list-filtered');
        });
    }

    public function test_client_can_navigate_domain_tabs(): void
    {
        $user = User::factory()->create();
        $client = Client::factory()->create(['user_id' => $user->id]);
        $domain = Domain::factory()->create([
            'client_id' => $client->id,
            'domain' => 'example.com',
            'status' => 'active',
            'nameservers' => ['ns1.example.com', 'ns2.example.com'],
        ]);

        $this->browse(function (Browser $browser) use ($user, $domain) {
            $browser->loginAs($user)
                ->visit("/client/domains/{$domain->id}")
                ->waitForText('example.com')
                ->assertSee('Active')

                ->assertSee('Overview')
                ->assertSee('Nameservers')
                ->assertSee('DNS Records')
                ->assertSee('Privacy & Lock')
                ->assertSee('WHOIS')
                ->assertSee('Billing')

                ->click('button:contains("Nameservers")')
                ->waitForText('Enter 2-5 nameservers')
                ->assertInputValue('input[placeholder*="ns1"]', 'ns1.example.com')
                ->screenshot('domains-nameservers-tab')

                ->click('button:contains("DNS Records")')
                ->waitForText('Add DNS Record')
                ->screenshot('domains-dns-tab')

                ->click('button:contains("Privacy & Lock")')
                ->waitForText('Privacy Protection')
                ->screenshot('domains-privacy-tab')

                ->click('button:contains("Billing")')
                ->waitForText('Domain Renewal')
                ->screenshot('domains-billing-tab');
        });
    }

    public function test_client_can_update_nameservers(): void
    {
        $user = User::factory()->create();
        $client = Client::factory()->create(['user_id' => $user->id]);
        $domain = Domain::factory()->create([
            'client_id' => $client->id,
            'status' => 'active',
            'nameservers' => ['ns1.old.com', 'ns2.old.com'],
        ]);

        $this->browse(function (Browser $browser) use ($user, $domain) {
            $browser->loginAs($user)
                ->visit("/client/domains/{$domain->id}")
                ->waitForText($domain->domain)
                ->click('button:contains("Nameservers")')
                ->waitForText('Enter 2-5 nameservers')

                ->clear('input[placeholder*="ns1"]')
                ->type('input[placeholder*="ns1"]', 'ns1.new.com')
                ->clear('input[placeholder*="ns2"]')
                ->type('input[placeholder*="ns2"]', 'ns2.new.com')

                ->press('Save Changes')
                ->waitForText('queued successfully')
                ->screenshot('domains-nameservers-updated')

                ->refresh()
                ->click('button:contains("Nameservers")')
                ->waitForText('Enter 2-5 nameservers')
                ->assertInputValue('input[placeholder*="ns1"]', 'ns1.new.com')
                ->assertInputValue('input[placeholder*="ns2"]', 'ns2.new.com');
        });
    }

    public function test_client_can_add_nameserver_field(): void
    {
        $user = User::factory()->create();
        $client = Client::factory()->create(['user_id' => $user->id]);
        $domain = Domain::factory()->create([
            'client_id' => $client->id,
            'status' => 'active',
            'nameservers' => ['ns1.example.com', 'ns2.example.com'],
        ]);

        $this->browse(function (Browser $browser) use ($user, $domain) {
            $browser->loginAs($user)
                ->visit("/client/domains/{$domain->id}")
                ->click('button:contains("Nameservers")')
                ->waitForText('Enter 2-5 nameservers')

                ->assertVisible('input[placeholder*="ns1"]')
                ->assertVisible('input[placeholder*="ns2"]')

                ->press('Add Nameserver')
                ->waitFor('input[placeholder*="ns3"]')
                ->assertVisible('input[placeholder*="ns3"]')
                ->screenshot('domains-nameserver-added')

                ->type('input[placeholder*="ns3"]', 'ns3.example.com')

                ->press('Add Nameserver')
                ->waitFor('input[placeholder*="ns4"]')

                ->press('Add Nameserver')
                ->waitFor('input[placeholder*="ns5"]')

                ->assertMissing('button:contains("Add Nameserver")');
        });
    }

    public function test_client_can_remove_nameserver_field(): void
    {
        $user = User::factory()->create();
        $client = Client::factory()->create(['user_id' => $user->id]);
        $domain = Domain::factory()->create([
            'client_id' => $client->id,
            'status' => 'active',
            'nameservers' => ['ns1.example.com', 'ns2.example.com', 'ns3.example.com'],
        ]);

        $this->browse(function (Browser $browser) use ($user, $domain) {
            $browser->loginAs($user)
                ->visit("/client/domains/{$domain->id}")
                ->click('button:contains("Nameservers")')
                ->waitForText('Enter 2-5 nameservers')

                ->assertVisible('input[placeholder*="ns3"]')

                ->press('Remove')
                ->pause(500)
                ->assertMissing('input[placeholder*="ns3"]')
                ->screenshot('domains-nameserver-removed');
        });
    }

    public function test_nameserver_validation_prevents_invalid_formats(): void
    {
        $user = User::factory()->create();
        $client = Client::factory()->create(['user_id' => $user->id]);
        $domain = Domain::factory()->create([
            'client_id' => $client->id,
            'status' => 'active',
            'nameservers' => ['ns1.example.com', 'ns2.example.com'],
        ]);

        $this->browse(function (Browser $browser) use ($user, $domain) {
            $browser->loginAs($user)
                ->visit("/client/domains/{$domain->id}")
                ->click('button:contains("Nameservers")')
                ->waitForText('Enter 2-5 nameservers')

                ->clear('input[placeholder*="ns1"]')
                ->type('input[placeholder*="ns1"]', 'invalid ns name with spaces')
                ->pause(500)
                ->assertSee('Invalid nameserver format')
                ->screenshot('domains-nameserver-validation');
        });
    }

    public function test_client_can_add_dns_record(): void
    {
        $user = User::factory()->create();
        $client = Client::factory()->create(['user_id' => $user->id]);
        $domain = Domain::factory()->create([
            'client_id' => $client->id,
            'status' => 'active',
        ]);

        $this->browse(function (Browser $browser) use ($user, $domain) {
            $browser->loginAs($user)
                ->visit("/client/domains/{$domain->id}")
                ->click('button:contains("DNS Records")')
                ->waitForText('Add DNS Record')

                ->press('Add DNS Record')
                ->waitForText('Record Type')

                ->select('select[name="type"]', 'A')
                ->type('input[name="host"]', '@')
                ->type('input[name="value"]', '192.0.2.1')
                ->type('input[name="ttl"]', '3600')

                ->screenshot('domains-dns-record-form')

                ->press('Save')
                ->waitForText('DNS record created successfully')
                ->screenshot('domains-dns-record-created')

                ->refresh()
                ->click('button:contains("DNS Records")')
                ->waitForText('Add DNS Record')
                ->assertSeeIn('table', 'A')
                ->assertSeeIn('table', '@')
                ->assertSeeIn('table', '192.0.2.1');
        });
    }

    public function test_client_can_edit_dns_record(): void
    {
        $user = User::factory()->create();
        $client = Client::factory()->create(['user_id' => $user->id]);
        $domain = Domain::factory()->create([
            'client_id' => $client->id,
            'status' => 'active',
        ]);

        $dnsRecord = $domain->dnsRecords()->create([
            'type' => 'A',
            'host' => '@',
            'value' => '192.0.2.1',
            'ttl' => 3600,
        ]);

        $this->browse(function (Browser $browser) use ($user, $domain) {
            $browser->loginAs($user)
                ->visit("/client/domains/{$domain->id}")
                ->click('button:contains("DNS Records")')
                ->waitForText('Add DNS Record')

                ->assertSeeIn('table', 'A')
                ->click('button:contains("Edit")')
                ->waitForText('Edit DNS Record')

                ->clear('input[name="value"]')
                ->type('input[name="value"]', '198.51.100.1')

                ->press('Save')
                ->waitForText('DNS record updated successfully')

                ->refresh()
                ->click('button:contains("DNS Records")')
                ->assertSeeIn('table', '198.51.100.1')
                ->screenshot('domains-dns-record-edited');
        });
    }

    public function test_client_can_delete_dns_record(): void
    {
        $user = User::factory()->create();
        $client = Client::factory()->create(['user_id' => $user->id]);
        $domain = Domain::factory()->create([
            'client_id' => $client->id,
            'status' => 'active',
        ]);

        $dnsRecord = $domain->dnsRecords()->create([
            'type' => 'A',
            'host' => '@',
            'value' => '192.0.2.1',
            'ttl' => 3600,
        ]);

        $this->browse(function (Browser $browser) use ($user, $domain) {
            $browser->loginAs($user)
                ->visit("/client/domains/{$domain->id}")
                ->click('button:contains("DNS Records")')
                ->waitForText('Add DNS Record')

                ->assertSeeIn('table', '192.0.2.1')

                ->click('button:contains("Delete")')
                ->waitForDialog()
                ->acceptDialog()
                ->waitForText('DNS record deleted successfully')

                ->refresh()
                ->click('button:contains("DNS Records")')
                ->assertDontSee('192.0.2.1')
                ->screenshot('domains-dns-record-deleted');
        });
    }

    public function test_client_can_toggle_privacy_protection(): void
    {
        $user = User::factory()->create();
        $client = Client::factory()->create(['user_id' => $user->id]);
        $domain = Domain::factory()->create([
            'client_id' => $client->id,
            'status' => 'active',
            'privacy_enabled' => false,
        ]);

        $this->browse(function (Browser $browser) use ($user, $domain) {
            $browser->loginAs($user)
                ->visit("/client/domains/{$domain->id}")
                ->click('button:contains("Privacy & Lock")')
                ->waitForText('Privacy Protection')

                ->assertSee('Hide your personal information')

                ->click('button.bg-gray-200:first-of-type')
                ->waitForText('queued')
                ->screenshot('domains-privacy-enabled')

                ->refresh()
                ->click('button:contains("Privacy & Lock")')
                ->pause(500);
        });
    }

    public function test_client_can_toggle_domain_lock(): void
    {
        $user = User::factory()->create();
        $client = Client::factory()->create(['user_id' => $user->id]);
        $domain = Domain::factory()->create([
            'client_id' => $client->id,
            'status' => 'active',
            'lock_enabled' => true,
        ]);

        $this->browse(function (Browser $browser) use ($user, $domain) {
            $browser->loginAs($user)
                ->visit("/client/domains/{$domain->id}")
                ->click('button:contains("Privacy & Lock")')
                ->waitForText('Domain Lock')

                ->assertSee('Prevent unauthorized domain transfers')

                ->click('button.bg-blue-600:nth-of-type(2)')
                ->waitForText('queued')
                ->screenshot('domains-lock-disabled');
        });
    }

    public function test_client_can_view_whois_information(): void
    {
        $user = User::factory()->create();
        $client = Client::factory()->create(['user_id' => $user->id]);
        $domain = Domain::factory()->create([
            'client_id' => $client->id,
            'status' => 'active',
            'whois_data' => [
                'registrant_name' => 'John Doe',
                'registrant_email' => 'john@example.com',
                'registrant_org' => 'Example Corp',
                'created_date' => '2020-01-01',
                'updated_date' => '2024-01-01',
            ],
        ]);

        $this->browse(function (Browser $browser) use ($user, $domain) {
            $browser->loginAs($user)
                ->visit("/client/domains/{$domain->id}")
                ->click('button:contains("WHOIS")')
                ->waitForText('WHOIS Information')

                ->assertSee('Registrant Name')
                ->assertSee('John Doe')
                ->assertSee('john@example.com')
                ->assertSee('Example Corp')

                ->screenshot('domains-whois-data');
        });
    }

    public function test_client_can_create_renewal_invoice(): void
    {
        $user = User::factory()->create();
        $client = Client::factory()->create(['user_id' => $user->id]);
        $domain = Domain::factory()->create([
            'client_id' => $client->id,
            'status' => 'active',
            'expires_at' => now()->addDays(30),
        ]);

        $this->browse(function (Browser $browser) use ($user, $domain) {
            $browser->loginAs($user)
                ->visit("/client/domains/{$domain->id}")
                ->click('button:contains("Billing")')
                ->waitForText('Domain Renewal')

                ->assertSee('1')
                ->assertSee('2')
                ->assertSee('3')

                ->click('button:contains("2")')
                ->pause(500)
                ->assertSee('USD')

                ->screenshot('domains-renewal-pricing')

                ->press('Create Renewal Invoice')
                ->waitForText('created successfully')
                ->screenshot('domains-renewal-invoice-created');
        });
    }

    public function test_rtl_layout_renders_correctly_in_arabic(): void
    {
        $user = User::factory()->create();
        $client = Client::factory()->create(['user_id' => $user->id]);
        Domain::factory()->count(3)->create([
            'client_id' => $client->id,
        ]);

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                ->visit('/client/domains?locale=ar')
                ->waitForText('نطاقاتي')
                ->assertSee('النطاق')
                ->assertSee('المسجل')
                ->assertSee('الحالة')
                ->screenshot('domains-list-arabic-rtl')

                ->click('a:contains("إدارة")')
                ->waitForText('نظرة عامة')
                ->assertSee('خوادم الأسماء')
                ->assertSee('سجلات DNS')
                ->assertSee('الخصوصية والقفل')
                ->screenshot('domains-detail-arabic-rtl');
        });
    }

    public function test_french_translations_render_correctly(): void
    {
        $user = User::factory()->create();
        $client = Client::factory()->create(['user_id' => $user->id]);
        Domain::factory()->count(3)->create([
            'client_id' => $client->id,
        ]);

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                ->visit('/client/domains?locale=fr')
                ->waitForText('Mes Domaines')
                ->assertSee('Domaine')
                ->assertSee('Registraire')
                ->assertSee('Statut')
                ->screenshot('domains-list-french')

                ->click('a:contains("Gérer")')
                ->waitForText('Aperçu')
                ->assertSee('Serveurs de Noms')
                ->assertSee('Enregistrements DNS')
                ->screenshot('domains-detail-french');
        });
    }
}
