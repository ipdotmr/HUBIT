# HUBIT E2E Testing Guide

## Overview

Stage 4 Milestone A includes comprehensive end-to-end tests using Laravel Dusk with a **deterministic mock infrastructure** for CI/CD reliability.

## Quick Start

```bash
# 1. Copy Dusk environment
cp .env.dusk.local.example .env.dusk.local

# 2. Install Dusk ChromeDriver
php artisan dusk:chrome-driver

# 3. Run E2E tests
php artisan dusk

# Run specific test group
php artisan dusk --group=stage4
php artisan dusk --group=i18n
php artisan dusk --group=security
```

## Mock vs Live Testing

### PR #4a: Mock Mode (Current - Deterministic)

All external integrations use mocks for reliable CI/CD:

- **Stripe/PayPal**: Mock payment clients with realistic latency
- **Registrars**: Mock domain registration (Namecheap, Name.com, etc.)
- **Provisioners**: Mock cPanel/Plesk with fake accounts
- **No secrets required**: Runs in CI without real API keys

**Toggle**: `E2E_USE_MOCKS=true` in `.env.dusk.local`

### PR #4b: Live Sandbox Mode (Future - After Credentials)

Real sandbox API integration for final verification:

1. Set credentials in `/managit/settings/*` UI
2. Flip `settings.testing.use_mocks=false` in Settings Console
3. Re-run tests against live sandbox APIs
4. Verify real Stripe test payments, real domain checks, etc.

**Toggle**: `settings.testing.use_mocks=false` (via Settings Console UI)

## Test Coverage

### Domain Flow (`Stage4DomainFlowTest`)
- ✅ Search domain availability (mock registrar)
- ✅ Add domain to cart
- ✅ Checkout with mock Stripe
- ✅ Payment success → Domain Active
- ✅ Invoice marked Paid
- ✅ i18n: EN/AR/FR + RTL verified

### Hosting Flow (`Stage4HostingFlowTest`)
- ✅ Add hosting product to cart
- ✅ Checkout with mock Stripe
- ✅ Payment success → cPanel provision job
- ✅ Service Active with credentials
- ✅ Product mapping auto-applied

### Wallet Flow (`Stage4WalletFlowTest`)
- ✅ Top-up wallet (mock Stripe)
- ✅ Wallet balance increases
- ✅ Pay invoice via wallet
- ✅ Invoice Paid, balance decreases
- ✅ Atomic transactions verified

### Product Mapping (`Stage4ProductMappingTest`)
- ✅ Admin sets Product → Package mapping
- ✅ Mapping saved and persisted
- ✅ Test provisioning succeeds
- ✅ New orders use mapped package

### Security Tests
- ✅ `/managit/` obscured from `/admin/`
- ✅ `/admin/*` returns 404 (honeypot)
- ✅ `robots.txt` disallows `/managit/`
- ✅ Rate limiting verified (60/min)

## Mock Implementation

### MockFactory (`app/Services/Testing/MockFactory.php`)

Central factory routing to mocks based on `E2E_USE_MOCKS` flag:

```php
use App\Services\Testing\MockFactory;

// Automatically uses mock or real based on setting
$stripe = MockFactory::stripe();
$registrar = MockFactory::registrar('namecheap');
$provisioner = MockFactory::provisioner('cpanel');
```

### Mock Clients

- **MockStripeClient**: Checkout sessions, payment intents, balance
- **MockPayPalClient**: Orders, captures
- **MockRegistrarClient**: Availability, register, renew, transfer, EPP, DNS, lock, privacy
- **MockProvisionerClient**: Create, suspend, unsuspend, terminate, upgrade, usage

All mocks include **realistic latency** (100-800ms) to simulate real API behavior.

## CI/CD Integration

### GitHub Actions Workflow

```yaml
name: E2E Tests

on: [push, pull_request]

jobs:
  dusk:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v3
      - name: Setup PHP
        uses: shivammathur/setup-php@v2
        with:
          php-version: '8.3'
      - name: Install dependencies
        run: composer install
      - name: Run Dusk tests
        env:
          E2E_USE_MOCKS: true
        run: php artisan dusk
```

No secrets needed for mock mode!

## Troubleshooting

### ChromeDriver Issues
```bash
# Update ChromeDriver
php artisan dusk:chrome-driver --detect

# Or specify version
php artisan dusk:chrome-driver 141
```

### Database Issues
```bash
# Fresh test database
php artisan migrate:fresh --env=dusk.local
```

### Headless Mode
```bash
# Run with visible browser (for debugging)
# In .env.dusk.local:
DUSK_HEADLESS=false
```

## Next Steps

After PR #4a merge:
1. Configure sandbox credentials in `/managit/settings/*`
2. Set `settings.testing.use_mocks=false`
3. Run PR #4b live-sandbox verification
4. Record live demo GIF
