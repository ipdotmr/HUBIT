# Stage 4 Milestone A - E2E Test Plan

## Test Coverage

This directory will contain Dusk E2E tests for Stage 4 Milestone A features.

### Required Test Flows

#### 1. Domain Registration Flow
- Navigate to `/domains/search`
- Search for an available domain
- Add domain to cart
- Proceed to checkout (Stripe test mode)
- Complete payment
- Verify domain shows as Active in `/dashboard/domains`
- Verify invoice marked as Paid
- Verify registrar job queued

#### 2. Hosting Provisioning Flow
- Add hosting product to cart
- Checkout with Stripe test payment
- Verify service provisioned via cPanel/Plesk
- Verify service shows as Active in `/dashboard/services`
- Verify credentials available

#### 3. Wallet Top-up & Payment Flow
- Navigate to `/dashboard/wallet`
- Top up wallet with Stripe
- Verify wallet balance increases
- Pay invoice using wallet balance
- Verify invoice marked as Paid
- Verify wallet balance decreases
- Verify wallet transaction recorded

#### 4. Product Mapping (Admin)
- Login as admin
- Navigate to `/managit/products/mapping`
- Map product to cPanel package
- Test provisioning
- Create client order
- Verify mapped package used in provisioning

### i18n & RTL Tests
- Verify all pages render in English (default)
- Switch to Arabic (`/language/ar`)
- Verify RTL layout (`dir="rtl"`)
- Verify no console errors
- Test French locale

### Security & Performance Tests
- Verify `/managit/` rate limiting (60/min)
- Verify `/admin/*` returns 404 (honeypot)
- Verify `robots.txt` disallows `/managit/`
- Lighthouse mobile score ≥ 95 for:
  - `/domains/search`
  - `/dashboard/services`

## Running Tests

```bash
# Set test environment variables
cp .env.dusk.example .env.dusk.local
# Add Stripe test keys to .env.dusk.local

# Run all Dusk tests
php artisan dusk

# Run specific test
php artisan dusk tests/Browser/DomainFlowTest.php
```

## Test Environment Setup

Required in `.env.dusk.local`:
```
STRIPE_TEST_KEY=pk_test_...
STRIPE_TEST_SECRET=sk_test_...
CPANEL_HOST=test.whm.local
CPANEL_API_TOKEN=...
```

## Notes

- Tests use DatabaseMigrations trait (fresh DB for each test)
- Stripe test card: 4242 4242 4242 4242
- cPanel/Plesk tests require staging provisioner access
- Wallet tests verify atomic DB transactions
