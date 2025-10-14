<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Organization;
use App\Models\Product;
use App\Models\Service;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DemoSeeder extends Seeder
{
    public function run(): void
    {
        $org = Organization::create([
            'name' => 'Demo Organization',
            'slug' => 'demo-org',
            'email' => 'contact@demo.com',
            'phone' => '+1234567890',
            'address' => '123 Demo Street',
            'city' => 'Demo City',
            'country' => 'US',
            'currency' => 'USD',
            'locale' => 'en',
            'timezone' => 'UTC',
            'is_active' => true,
        ]);

        $adminUser = User::create([
            'name' => 'Admin User',
            'email' => 'admin@hubit.test',
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
            'remember_token' => Str::random(10),
        ]);

        $clientUser1 = User::create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
            'remember_token' => Str::random(10),
        ]);

        $clientUser2 = User::create([
            'name' => 'Jane Smith',
            'email' => 'jane@example.com',
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
            'remember_token' => Str::random(10),
        ]);

        $client1 = Client::create([
            'organization_id' => $org->id,
            'user_id' => $clientUser1->id,
            'type' => 'individual',
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john@example.com',
            'phone' => '+1234567891',
            'address' => '456 Client Ave',
            'city' => 'Client City',
            'country' => 'US',
            'status' => 'active',
            'credit_balance' => 0,
            'language' => 'en',
        ]);

        $client2 = Client::create([
            'organization_id' => $org->id,
            'user_id' => $clientUser2->id,
            'type' => 'company',
            'company_name' => 'Acme Corp',
            'first_name' => 'Jane',
            'last_name' => 'Smith',
            'email' => 'jane@example.com',
            'phone' => '+1234567892',
            'address' => '789 Business Blvd',
            'city' => 'Commerce City',
            'country' => 'US',
            'status' => 'active',
            'credit_balance' => 50.00,
            'language' => 'en',
        ]);

        $sharedHosting = Product::create([
            'group' => 'hosting',
            'name' => 'Shared Hosting Basic',
            'slug' => 'shared-hosting-basic',
            'description' => 'Perfect for small websites and blogs',
            'is_active' => true,
            'billing_cycles' => ['monthly' => 9.99, 'annual' => 99.99],
            'base_price' => 9.99,
            'config_options' => [
                'disk' => '10GB',
                'bandwidth' => '100GB',
                'databases' => 5,
                'email_accounts' => 10,
            ],
            'provisioner' => 'Cpanel',
            'provisioner_config' => [
                'package' => 'BASIC',
            ],
            'sort_order' => 1,
        ]);

        $vpsBasic = Product::create([
            'group' => 'vps',
            'name' => 'VPS Basic',
            'slug' => 'vps-basic',
            'description' => 'Entry-level VPS with full root access',
            'is_active' => true,
            'billing_cycles' => ['monthly' => 29.99, 'annual' => 299.99],
            'base_price' => 29.99,
            'config_options' => [
                'cpu' => '2 Cores',
                'ram' => '2GB',
                'disk' => '50GB SSD',
                'bandwidth' => '2TB',
            ],
            'provisioner' => 'Cpanel',
            'provisioner_config' => [
                'package' => 'VPS_BASIC',
            ],
            'sort_order' => 2,
        ]);

        $vpsPro = Product::create([
            'group' => 'vps',
            'name' => 'VPS Pro',
            'slug' => 'vps-pro',
            'description' => 'High-performance VPS for demanding applications',
            'is_active' => true,
            'billing_cycles' => ['monthly' => 59.99, 'annual' => 599.99],
            'base_price' => 59.99,
            'config_options' => [
                'cpu' => '4 Cores',
                'ram' => '8GB',
                'disk' => '200GB SSD',
                'bandwidth' => '5TB',
            ],
            'provisioner' => 'Cpanel',
            'provisioner_config' => [
                'package' => 'VPS_PRO',
            ],
            'sort_order' => 3,
        ]);

        $service1 = Service::create([
            'client_id' => $client1->id,
            'product_id' => $sharedHosting->id,
            'status' => 'active',
            'next_due_at' => now()->addMonth(),
            'billing_cycle' => 'monthly',
            'recurring_amount' => 9.99,
            'provisioner' => 'Cpanel',
            'provision_ref' => [
                'username' => 'johndoe123',
                'domain' => 'johndoe.example.com',
                'package' => 'BASIC',
            ],
            'credentials' => encrypt([
                'username' => 'johndoe123',
                'password' => Str::random(16),
            ]),
            'config' => [
                'domain' => 'johndoe.example.com',
            ],
        ]);

        $service2 = Service::create([
            'client_id' => $client2->id,
            'product_id' => $vpsBasic->id,
            'status' => 'active',
            'next_due_at' => now()->addMonth(),
            'billing_cycle' => 'annual',
            'recurring_amount' => 299.99,
            'provisioner' => 'Cpanel',
            'provision_ref' => [
                'username' => 'acme456',
                'domain' => 'acme.example.com',
                'package' => 'VPS_BASIC',
            ],
            'credentials' => encrypt([
                'username' => 'acme456',
                'password' => Str::random(16),
            ]),
            'config' => [
                'domain' => 'acme.example.com',
            ],
        ]);

        $invoice1 = Invoice::create([
            'client_id' => $client1->id,
            'number' => 'INV-' . str_pad(1, 6, '0', STR_PAD_LEFT),
            'status' => 'paid',
            'issue_date' => now()->subDays(15),
            'due_date' => now()->subDays(5),
            'subtotal' => 9.99,
            'tax' => 0,
            'discount' => 0,
            'total' => 9.99,
            'paid' => 9.99,
            'currency' => 'USD',
        ]);

        InvoiceItem::create([
            'invoice_id' => $invoice1->id,
            'type' => 'product',
            'description' => 'Shared Hosting Basic - Monthly',
            'quantity' => 1,
            'unit_amount' => 9.99,
            'amount' => 9.99,
        ]);

        $invoice2 = Invoice::create([
            'client_id' => $client2->id,
            'number' => 'INV-' . str_pad(2, 6, '0', STR_PAD_LEFT),
            'status' => 'open',
            'issue_date' => now()->subDays(5),
            'due_date' => now()->addDays(10),
            'subtotal' => 299.99,
            'tax' => 30.00,
            'discount' => 0,
            'total' => 329.99,
            'paid' => 0,
            'currency' => 'USD',
        ]);

        InvoiceItem::create([
            'invoice_id' => $invoice2->id,
            'type' => 'product',
            'description' => 'VPS Basic - Annual',
            'quantity' => 1,
            'unit_amount' => 299.99,
            'amount' => 299.99,
        ]);

        InvoiceItem::create([
            'invoice_id' => $invoice2->id,
            'type' => 'tax',
            'description' => 'Tax (10%)',
            'quantity' => 1,
            'unit_amount' => 30.00,
            'amount' => 30.00,
        ]);

        $this->command->info('Demo data seeded successfully!');
        $this->command->info('Admin: admin@hubit.test / password');
        $this->command->info('Client 1: john@example.com / password');
        $this->command->info('Client 2: jane@example.com / password');
    }
}
