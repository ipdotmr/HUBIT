# HUBIT Development Notes

## Lessons Learned from Milestone B Implementation

### i18n Flow (EN/AR/FR)

**Structure**:
```
lang/
  en/
    common.php       # Shared UI strings
    domains.php      # Feature-specific strings
  ar/
    common.php
    domains.php
  fr/
    common.php
    domains.php
```

**Best Practices**:
1. **Separate by feature**: Keep feature-specific strings in dedicated files
2. **Use namespaces**: Access via `$t('domains.key')` or `$t('common.key')`
3. **RTL considerations**: 
   - Arabic text flows right-to-left automatically
   - Test margin/padding alignment
   - Icon positions may need adjustment
4. **Placeholders**: Use `:variable` syntax for dynamic content (e.g., `expiry_warning: 'Expires on :date'`)
5. **Pluralization**: Handle singular/plural forms properly

**Vue Usage**:
```vue
<template>
  <h1>{{ $t('domains.my_domains') }}</h1>
  <p>{{ $t('domains.expiry_warning', { date: formattedDate }) }}</p>
</template>
```

### RTL Nuances

**Tailwind RTL**:
- Use logical properties where possible: `ms-4` (margin-start) instead of `ml-4`
- Test all layouts with `?locale=ar` query parameter
- Icons: Some need explicit RTL handling

**Common Issues**:
- Tables: Column order stays LTR but text aligns right
- Modals: Verify close button positioning
- Forms: Label alignment needs testing
- Numbers/Dates: Use `toLocaleDateString()` for proper formatting

**Testing Checklist**:
- [ ] Navigation menu renders correctly
- [ ] Form fields align properly
- [ ] Tables display cleanly
- [ ] Modals/dialogs work
- [ ] Breadcrumbs flow correctly

### Dusk E2E with Mocks

**Mock Strategy**:
```php
// In test setUp()
config(['testing.use_mocks' => true]);

// MockRegistrarClient handles all API calls
// No external dependencies = fast, reliable tests
```

**Best Practices**:
1. **Use descriptive test names**: `test_client_can_toggle_privacy_protection()`
2. **Screenshot key states**: `->screenshot('domains-privacy-enabled')`
3. **Test user flows, not implementation**: Focus on what users do
4. **Verify async operations**: Use `waitForText()` after queued jobs
5. **Test i18n**: Include RTL and French translation tests

**Common Patterns**:
```php
// Wait for dynamic content
$browser->waitForText('Processing...')
        ->waitForText('Success');

// Test toggles
$browser->click('button.toggle')
        ->waitForText('queued')
        ->refresh()
        ->assertSee('Enabled');

// Verify validation
$browser->type('input', 'invalid data')
        ->pause(500)
        ->assertSee('Validation error');
```

### Pint Configuration

**Auto-fix before commit**:
```bash
./vendor/bin/pint
```

**Check without fixing**:
```bash
./vendor/bin/pint --test
```

**Common Issues**:
- **no_whitespace_in_blank_line**: Remove spaces from empty lines
- **class_definition**: Proper class declaration formatting
- **braces_position**: Opening braces on same line

**CI Integration**:
- Always run Pint locally before pushing
- CI runs `pint --test` to verify
- Fix issues immediately to avoid blocking PRs

### Component Organization

**Vue Component Structure**:
```
Pages/
  Client/
    Domains/
      Index.vue              # List page
      Show.vue               # Detail page with tabs
      Components/
        OverviewTab.vue
        NameserversTab.vue
        DnsRecordsTab.vue
        PrivacyLockTab.vue
        WhoisTab.vue
        BillingTab.vue
```

**Benefits**:
- Lazy loading per tab
- Clear separation of concerns
- Easy to test individually
- Reusable across features

**Tab Pattern**:
```vue
// Show.vue
<script setup>
const tabs = [
  { id: 'overview', component: defineAsyncComponent(() => import('./Components/OverviewTab.vue')) },
  // ...
];
</script>

<template>
  <Suspense>
    <component :is="activeTab.component" :domain="domain" />
  </Suspense>
</template>
```

### Performance Optimizations

**Lazy Loading**:
- Use `defineAsyncComponent()` for tabs
- Reduces initial bundle size
- Faster page load

**Virtualization**:
- For large lists (>200 DNS records)
- Only render visible items
- Smooth scrolling

**Debouncing**:
- Search inputs
- Filter changes
- Form validation

**Code Splitting**:
```javascript
// Vite automatically splits by page
// Each route = separate chunk
// Shared code in vendor chunk
```

### Backend Queue Jobs

**Idempotency Pattern**:
```php
public function handle()
{
    $key = $this->getIdempotencyKey();
    
    if ($this->wasAlreadyProcessed($key)) {
        return;
    }
    
    // Do work
    
    $this->markAsProcessed($key);
}
```

**Retry Logic**:
```php
// In job class
public $tries = 3;
public $backoff = [10, 30, 60]; // seconds
```

**Logging**:
```php
$logger->start('domain.nameservers.update', $this->domain);
try {
    // work
    $logger->success('domain.nameservers.update', $this->domain, $result);
} catch (\Exception $e) {
    $logger->failure('domain.nameservers.update', $this->domain, $e);
}
```

### Settings Console Integration

**Reading Settings**:
```php
$credentials = settings()->get('domains.registrars.namecheap');
$apiKey = $credentials['api_key'] ?? null;
```

**Testing Mode**:
```php
if (settings()->get('testing.use_mocks')) {
    return app(MockRegistrarClient::class);
}
return app(NamecheapRegistrar::class);
```

**Security**:
- All credentials encrypted at rest
- Access via Settings Console UI
- No hardcoded secrets
- Environment-specific configs

### Database Patterns

**JSON Columns**:
```php
// Migration
$table->json('nameservers')->nullable();

// Model
protected $casts = [
    'nameservers' => 'array',
];

// Usage
$domain->nameservers = ['ns1.example.com', 'ns2.example.com'];
```

**Relationships**:
```php
// Eager loading
$domains = Domain::with(['dnsRecords', 'renewals'])->get();

// Prevent N+1
$domain->load('dnsRecords');
```

**Indexes**:
```php
// High-query columns
$table->index(['client_id', 'status']);
$table->index('expires_at');
```

### Common Gotchas

1. **Locale Detection**: Use middleware or query param, not session-only
2. **CSRF**: Ensure Inertia form includes token
3. **Rate Limiting**: Apply to all mutation endpoints
4. **Validation**: Backend + frontend for best UX
5. **Async UI**: Show spinners, disable buttons during processing
6. **Error Handling**: Toast messages for user feedback
7. **Permissions**: Always check via Policy gates
8. **Mock Data**: Use factories for consistent test data

### Code Quality Checklist

Before opening PR:
- [ ] Run `./vendor/bin/pint`
- [ ] Run `php artisan test`
- [ ] Run `php artisan dusk` (with mocks)
- [ ] Test EN/AR/FR translations
- [ ] Verify RTL layout
- [ ] Check Lighthouse score (mobile ≥95)
- [ ] Review screenshots
- [ ] Update documentation

### Next Steps for PR B3

**Features to Implement**:
1. **My Services**:
   - List with filters (product type, status)
   - Detail page with actions (suspend, unsuspend, upgrade)
   - Usage metrics display
   - Credentials viewer

2. **Wallet**:
   - Balance display (multi-currency)
   - Transaction history
   - Top-up widget
   - Auto-recharge settings

3. **Reports**:
   - Service usage by month
   - Spending breakdown
   - Upcoming renewals
   - Invoice history

**Patterns to Reuse**:
- Tab navigation from Domains
- i18n structure (EN/AR/FR)
- Dusk testing with mocks
- Settings Console integration
- Queue jobs for async operations

---

**Last Updated**: 2025-10-16
**Author**: Devin (AI Engineer)
**Milestone**: B - Self-Service Features
