# Stage 4 Demo Videos

## Mock Demonstrations (PR #4a)

### stage4a-mock-demo.gif
**Status**: Pending recording  
**Duration**: 45-60 seconds  
**Content**:
1. Domain Flow: Search domain → Add to cart → Checkout (mock Stripe) → Domain Active in "My Domains"
2. Hosting Flow: Purchase hosting → Provision via mock cPanel → Service Active in "My Services"

**Requirements**:
- `settings.testing.use_mocks = true`
- Deterministic mock responses
- Clean UI, no errors

### How to Record
```bash
# Use any screen recorder (e.g., Kap, LICEcap, ScreenToGif)
# Navigate flows manually or automate via Dusk with screenshots
php artisan dusk --filter=test_domain_registration_flow_with_mocks
```

## Live Sandbox Demonstrations (PR #4b - Future)

### stage4b-live-demo.gif  
**Status**: Awaiting sandbox credentials configuration  
**Duration**: 45-60 seconds  
**Content**: Same flows but with real Stripe test mode + real registrar sandbox

**Requirements**:
- Configure credentials in `/managit/settings/*`
- Set `settings.testing.use_mocks = false`
- Real API calls to sandbox environments
