# HUBIT - Hosting & Billing Platform

**HUBIT** is a comprehensive web hosting and billing management system with feature parity to WHMCS. Built with Laravel 11, PostgreSQL, Vue 3, and modern tooling, HUBIT provides a complete solution for hosting providers to manage clients, products, billing, provisioning, and support.

🌐 **Live Demo**: [my.ip.mr](https://my.ip.mr)  
📚 **Documentation**: [docs.ip.mr](https://docs.ip.mr)  
🎯 **Status**: Phase 1 MVP - In Development

## Branching Model

The actively maintained source of truth lives on the `main` branch. All feature and
stabilization work is merged into `main` once reviewed, so if you cannot find a
feature branch in the remote repository, make sure you are checking out `main` to
access the complete, production-ready project history.

## Features

### Phase 1 - MVP (Current)
- ✅ **Authentication & Authorization**
  - Email/password authentication with email verification
  - Two-factor authentication (TOTP)
  - Organizations/Clients with multi-user accounts
  - Role-based permissions (RBAC)

- ✅ **Product Catalog & Checkout**
  - Hosting products with configurable options
  - Shopping cart with coupon codes
  - Tax/VAT calculation
  - Stripe payment integration

- ✅ **Billing System**
  - Invoice generation with PDF export
  - Payment receipts and tracking
  - Credit notes and refunds
  - Multi-currency support (USD/EUR)

- ✅ **Provisioning**
  - cPanel/WHM integration (create, suspend, terminate, password reset)
  - Abstract provisioner interface for extensibility
  - Automated account provisioning on payment
  - Usage synchronization

- ✅ **Client Area**
  - Dashboard with services and billing overview
  - Service management and credentials
  - Invoice payment with saved payment methods
  - Support ticket system

- ✅ **Admin Panel**
  - Company settings and configuration
  - Product and order management
  - Client management
  - Financial reports (MRR, active services)
  - Audit logging

- ✅ **Theming & Internationalization**
  - Theme engine with dark mode
  - HUBIT Classic theme
  - English, Arabic (RTL), French support
  - Responsive mobile-first design

### Phase 2 - Core Parity Expansion (Planned)
- Plesk provisioning module
- Domain registrar integrations (Namecheap, ResellerClub)
- PayPal payment gateway
- Enhanced ticketing (departments, SLAs, canned replies)
- Knowledge base system
- Affiliates program
- 5 total themes including "IPMR" matched theme
- Full multilingual translations

### Phase 3 - Licensing & Hardening (Planned)
- Self-hosted licensing server
- Advanced security hardening
- OpenAPI 3.1 specification
- PHP & JavaScript SDKs
- Module marketplace
- Production-grade monitoring

## Technology Stack

### Backend
- **Framework**: Laravel 11
- **PHP**: 8.3
- **Database**: PostgreSQL 16
- **Cache/Queue**: Redis 7
- **Queue Processing**: Laravel Horizon

### Frontend
- **Framework**: Vue 3 (Composition API)
- **Server-Side**: Inertia.js with SSR
- **Build Tool**: Vite
- **Styling**: TailwindCSS 4
- **i18n**: Vue I18n

### DevOps
- **Containerization**: Docker & Docker Compose
- **CI/CD**: GitHub Actions
- **Testing**: Pest (unit), Dusk (E2E)
- **Code Quality**: PHPStan, ESLint

## Quick Start

### Prerequisites
- PHP 8.3+
- PostgreSQL 16+
- Redis 7+
- Node.js 18+
- Composer 2.x
- Docker & Docker Compose (optional)

### Installation

1. **Clone the repository**
   ```bash
   git clone https://github.com/ipdotmr/HUBIT.git
   cd HUBIT
   ```

2. **Set up environment**
   ```bash
   cd app
   cp .env.example .env
   php artisan key:generate
   ```

3. **Configure database**
   Edit `.env` and set your PostgreSQL credentials:
   ```
   DB_CONNECTION=pgsql
   DB_HOST=127.0.0.1
   DB_PORT=5432
   DB_DATABASE=hubit
   DB_USERNAME=hubit
   DB_PASSWORD=your_password
   ```

4. **Install dependencies**
   ```bash
   composer install
   npm install
   ```

5. **Run migrations**
   ```bash
   php artisan migrate
   ```

6. **Build frontend**
   ```bash
   npm run build
   ```

7. **Start the application**
   ```bash
   php artisan serve
   ```

Visit: `http://localhost:8000`

### Using Docker

```bash
docker-compose up -d
```

The application will be available at `http://localhost:8000`

## Web Installer

The production installer offers a secure, guided setup without requiring manual file edits.

1. **Deploy the codebase** – Upload the repository contents to your hosting environment (the Laravel application lives in the `app/` directory).
2. **Launch the wizard** – Visit `/install` in your browser. The installer is only available while it is unlocked.
3. **Complete the steps** – Run preflight checks, enter your `.env` values, create the first administrator, and execute the installation.
4. **Lock-in** – On success the installer writes `storage/framework/.installed`. Subsequent visits to `/install` respond with `404` to prevent reuse.

> To re-run the installer (not recommended), delete the marker file at `storage/framework/.installed` and reload `/install`.

## Deployment runbook (manual)

Execute the following commands from the application root on every deployment:

```bash
php artisan migrate --force
php artisan optimize
php artisan queue:restart || true
php artisan horizon:terminate || true
```

## Configuration

### Payment Gateway (Stripe)

Add your Stripe credentials to `.env`:
```
STRIPE_KEY=pk_test_...
STRIPE_SECRET=sk_test_...
STRIPE_WEBHOOK_SECRET=whsec_...
```

Set up webhook endpoint at: `https://yourdomain.com/webhooks/stripe`

### cPanel Provisioning

Configure your WHM/cPanel server in `.env`:
```
CPANEL_HOST=cpanel.yourdomain.com
CPANEL_API_TOKEN=your_api_token_here
CPANEL_USE_SSL=true
CPANEL_DEFAULT_PACKAGE=HUBIT_BASIC
```

### Email Configuration

Configure SMTP in `.env`:
```
MAIL_MAILER=smtp
MAIL_HOST=smtp.example.com
MAIL_PORT=587
MAIL_USERNAME=your_username
MAIL_PASSWORD=your_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@yourdomain.com
```

## Development

### Running tests
```bash
php artisan test
```

### Running E2E tests
```bash
php artisan dusk
```

### Code style
```bash
./vendor/bin/pint
```

### Frontend development
```bash
npm run dev
```

## Project Structure

```
HUBIT/
├── app/                # Laravel application
│   ├── app/           # Application code
│   ├── config/        # Configuration
│   ├── database/      # Migrations, seeders
│   ├── resources/     # Views, Vue components
│   ├── routes/        # Route definitions
│   └── tests/         # Tests
├── docs/              # Docusaurus documentation
├── deploy/            # Deployment configurations
│   ├── Dockerfile
│   ├── nginx.conf
│   └── docker-compose.prod.yml
└── README.md
```

## API Documentation

API documentation is available at `/api/docs` when the application is running.

## Contributing

We welcome contributions! Please see [CONTRIBUTING.md](CONTRIBUTING.md) for guidelines.

## Security

If you discover a security vulnerability, please email security@ip.mr. All security vulnerabilities will be promptly addressed.

## License

HUBIT is licensed under the [Apache License 2.0](LICENSE).

## Roadmap

### Phase 1 - MVP (Weeks 1-3) ✅ In Progress
- [x] Project scaffolding
- [x] Docker & CI/CD setup
- [ ] Authentication & 2FA
- [ ] Organizations & Clients
- [ ] Product catalog
- [ ] Stripe integration
- [ ] cPanel provisioning
- [ ] Client & Admin areas
- [ ] Basic documentation

### Phase 2 - Core Parity (Weeks 4-9)
- [ ] Plesk module
- [ ] Domain registrars
- [ ] PayPal integration
- [ ] Knowledge base
- [ ] Affiliates
- [ ] 5 themes
- [ ] Full i18n

### Phase 3 - Licensing (Weeks 10-13)
- [ ] Licensing server
- [ ] Security hardening
- [ ] OpenAPI spec
- [ ] SDKs
- [ ] Marketplace
- [ ] v1.0.0 release

## Support

- 📖 [Documentation](https://docs.ip.mr)
- 💬 [GitHub Discussions](https://github.com/ipdotmr/HUBIT/discussions)
- 🐛 [Issue Tracker](https://github.com/ipdotmr/HUBIT/issues)

## Acknowledgments

Built with modern open-source technologies. HUBIT provides functional parity with WHMCS while being fully open source and extensible.

---

**Made with ❤️ by IP MR**  
🌐 [https://ip.mr](https://ip.mr)
