# HUBIT Production Deployment Guide

## Prerequisites

- Ubuntu 22.04 LTS server
- Docker & Docker Compose installed
- Domain DNS configured:
  - `cp.ip.mr` → Server IP (194.140.197.36)
  - `docs.ip.mr` → Server IP (194.140.197.36)
- Root or sudo access

## Quick Start

```bash
# 1. Clone the repository
git clone https://github.com/ipdotmr/HUBIT.git
cd HUBIT
git checkout devin/1760458343-initial-scaffold

# 2. Set up SSL certificates (run as root)
sudo ./deploy/bin/setup-ssl.sh

# 3. Configure environment
cp deploy/env/.env.production deploy/env/.env.production.local
nano deploy/env/.env.production.local
# Update: DB_PASSWORD, MAIL_*, STRIPE_*, CPANEL_API_TOKEN

# 4. Deploy
./deploy/bin/deploy-production.sh
```

## Detailed Setup

### 1. Server Preparation

```bash
# Update system
sudo apt-get update && sudo apt-get upgrade -y

# Install Docker
curl -fsSL https://get.docker.com -o get-docker.sh
sudo sh get-docker.sh

# Install Docker Compose
sudo apt-get install docker-compose-plugin

# Add user to docker group
sudo usermod -aG docker $USER
newgrp docker
```

### 2. DNS Configuration

Ensure the following DNS A records are configured:

| Domain | Type | Value |
|--------|------|-------|
| cp.ip.mr | A | 194.140.197.36 |
| docs.ip.mr | A | 194.140.197.36 |

Verify DNS propagation:
```bash
dig cp.ip.mr +short
dig docs.ip.mr +short
```

### 3. SSL Certificate Setup

```bash
# Run SSL setup script (as root)
sudo ./deploy/bin/setup-ssl.sh
```

This script will:
- Install certbot
- Generate SSL certificates for cp.ip.mr and docs.ip.mr
- Copy certificates to `deploy/ssl/`
- Set up automatic renewal

### 4. Environment Configuration

Edit `deploy/env/.env.production`:

```env
# Application
APP_NAME=HUBIT
APP_ENV=production
APP_URL=https://cp.ip.mr
APP_DEBUG=false

# Database
DB_DATABASE=hubit
DB_USERNAME=hubit
DB_PASSWORD=CHANGE_ME_SECURE_PASSWORD

# Mail
MAIL_HOST=smtp.ip.mr
MAIL_USERNAME=notify@ip.mr
MAIL_PASSWORD=CHANGE_ME

# Stripe
STRIPE_KEY=pk_live_xxxxx
STRIPE_SECRET=sk_live_xxxxx
STRIPE_WEBHOOK_SECRET=whsec_xxxxx

# cPanel/WHM
CPANEL_HOST=194.140.197.36
CPANEL_API_TOKEN=CHANGE_ME_API_TOKEN
CPANEL_DEFAULT_PACKAGE=HUBIT_BASIC
```

### 5. Deploy Application

```bash
./deploy/bin/deploy-production.sh
```

This script will:
1. Pull latest code
2. Build Docker images
3. Start containers
4. Generate application key
5. Run migrations
6. Seed demo data
7. Optimize application
8. Restart queue workers

## Post-Deployment

### 1. Verify Services

```bash
# Check container status
docker-compose -f docker-compose.production.yml ps

# Check logs
docker-compose -f docker-compose.production.yml logs -f app
docker-compose -f docker-compose.production.yml logs -f nginx
docker-compose -f docker-compose.production.yml logs -f horizon
```

### 2. Test Application

```bash
# Test HTTPS
curl -I https://cp.ip.mr

# Test documentation
curl -I https://docs.ip.mr
```

### 3. Configure Stripe Webhook

1. Go to Stripe Dashboard → Developers → Webhooks
2. Add endpoint: `https://cp.ip.mr/webhooks/stripe`
3. Select events:
   - `checkout.session.completed`
   - `payment_intent.payment_failed`
   - `charge.refunded`
4. Copy webhook secret to `.env.production`

### 4. Configure cPanel WHM

1. Login to WHM at `https://194.140.197.36:2087`
2. Generate API token: WHM → Development → Manage API Tokens
3. Create package `HUBIT_BASIC` with:
   - Disk quota: 10GB
   - Bandwidth: 100GB
   - Databases: 5
   - Email accounts: 10
4. Update `.env.production` with API token

### 5. Change Default Passwords

```bash
# Connect to app container
docker-compose -f docker-compose.production.yml exec app bash

# Update admin password
php artisan tinker
>>> $user = App\Models\User::where('email', 'admin@hubit.test')->first();
>>> $user->password = bcrypt('NEW_SECURE_PASSWORD');
>>> $user->save();
>>> exit
```

## Maintenance

### Update Application

```bash
git pull origin devin/1760458343-initial-scaffold
./deploy/bin/deploy-production.sh
```

### Backup Database

```bash
docker-compose -f docker-compose.production.yml exec postgres pg_dump -U hubit hubit > backup-$(date +%Y%m%d-%H%M%S).sql
```

### Restore Database

```bash
docker-compose -f docker-compose.production.yml exec -T postgres psql -U hubit hubit < backup-20250114-120000.sql
```

### View Logs

```bash
# Application logs
docker-compose -f docker-compose.production.yml logs -f app

# Nginx access logs
docker-compose -f docker-compose.production.yml exec nginx tail -f /var/log/nginx/cp.ip.mr.access.log

# Horizon (queue) logs
docker-compose -f docker-compose.production.yml logs -f horizon
```

### Restart Services

```bash
# Restart specific service
docker-compose -f docker-compose.production.yml restart app

# Restart all services
docker-compose -f docker-compose.production.yml restart

# Restart queue workers
docker-compose -f docker-compose.production.yml exec app php artisan queue:restart
```

## Troubleshooting

### SSL Certificate Issues

```bash
# Renew certificates manually
sudo certbot renew --force-renewal
sudo ./deploy/bin/setup-ssl.sh
```

### Database Connection Issues

```bash
# Check PostgreSQL logs
docker-compose -f docker-compose.production.yml logs postgres

# Test connection
docker-compose -f docker-compose.production.yml exec app php artisan tinker
>>> DB::connection()->getPdo();
```

### Queue Not Processing

```bash
# Restart Horizon
docker-compose -f docker-compose.production.yml restart horizon

# Check Horizon status
docker-compose -f docker-compose.production.yml exec app php artisan horizon:status
```

### Permission Issues

```bash
# Fix storage permissions
docker-compose -f docker-compose.production.yml exec app chmod -R 755 storage
docker-compose -f docker-compose.production.yml exec app chown -R www-data:www-data storage
```

## Security Checklist

- [ ] Change all default passwords
- [ ] Configure firewall (UFW):
  ```bash
  sudo ufw allow 22/tcp
  sudo ufw allow 80/tcp
  sudo ufw allow 443/tcp
  sudo ufw enable
  ```
- [ ] Enable fail2ban
- [ ] Set up regular backups
- [ ] Configure monitoring (optional: install Sentry)
- [ ] Review and restrict database access
- [ ] Enable 2FA for all admin accounts

## Performance Tuning

### PHP-FPM Optimization

Edit `deploy/php-fpm/www.conf`:

```ini
pm = dynamic
pm.max_children = 50
pm.start_servers = 10
pm.min_spare_servers = 5
pm.max_spare_servers = 20
```

### Redis Optimization

```bash
# Increase maxmemory
docker-compose -f docker-compose.production.yml exec redis redis-cli CONFIG SET maxmemory 256mb
docker-compose -f docker-compose.production.yml exec redis redis-cli CONFIG SET maxmemory-policy allkeys-lru
```

### PostgreSQL Optimization

```bash
# Edit postgresql.conf
docker-compose -f docker-compose.production.yml exec postgres bash
vi /var/lib/postgresql/data/postgresql.conf

# Add:
shared_buffers = 256MB
effective_cache_size = 1GB
```

## Monitoring

### Health Checks

```bash
# Application health
curl https://cp.ip.mr/health

# Database health
docker-compose -f docker-compose.production.yml exec postgres pg_isready

# Redis health
docker-compose -f docker-compose.production.yml exec redis redis-cli ping
```

### Metrics

```bash
# Queue metrics
docker-compose -f docker-compose.production.yml exec app php artisan horizon:status

# Cache hit rate
docker-compose -f docker-compose.production.yml exec redis redis-cli INFO stats | grep keyspace
```

## Support

For issues or questions:
- Email: admin@ip.mr
- Documentation: https://docs.ip.mr
- Repository: https://github.com/ipdotmr/HUBIT
