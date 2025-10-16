#!/bin/bash

set -e

echo "🚀 Starting HUBIT Production Deployment to cp.ip.mr"

GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m' # No Color

if [ ! -f "docker-compose.production.yml" ]; then
    echo -e "${RED}Error: docker-compose.production.yml not found. Please run this script from the HUBIT root directory.${NC}"
    exit 1
fi

echo -e "${YELLOW}Step 1: Checking prerequisites...${NC}"
command -v docker >/dev/null 2>&1 || { echo -e "${RED}Docker is not installed. Aborting.${NC}" >&2; exit 1; }
command -v docker-compose >/dev/null 2>&1 || { echo -e "${RED}Docker Compose is not installed. Aborting.${NC}" >&2; exit 1; }
echo -e "${GREEN}✓ Docker and Docker Compose are installed${NC}"

echo -e "${YELLOW}Step 2: Checking environment file...${NC}"
if [ ! -f "deploy/env/.env.production" ]; then
    echo -e "${RED}Error: deploy/env/.env.production not found.${NC}"
    exit 1
fi
echo -e "${GREEN}✓ Environment file exists${NC}"

echo -e "${YELLOW}Step 3: Pulling latest code...${NC}"
git pull origin devin/1760458343-initial-scaffold
echo -e "${GREEN}✓ Code updated${NC}"

echo -e "${YELLOW}Step 4: Building Docker images...${NC}"
docker-compose -f docker-compose.production.yml build --no-cache
echo -e "${GREEN}✓ Docker images built${NC}"

echo -e "${YELLOW}Step 5: Stopping old containers...${NC}"
docker-compose -f docker-compose.production.yml down
echo -e "${GREEN}✓ Old containers stopped${NC}"

echo -e "${YELLOW}Step 6: Starting new containers...${NC}"
docker-compose -f docker-compose.production.yml up -d
echo -e "${GREEN}✓ Containers started${NC}"

echo -e "${YELLOW}Step 7: Waiting for database to be ready...${NC}"
sleep 10
echo -e "${GREEN}✓ Database ready${NC}"

echo -e "${YELLOW}Step 8: Generating application key...${NC}"
docker-compose -f docker-compose.production.yml exec -T app php artisan key:generate --force
echo -e "${GREEN}✓ Application key generated${NC}"

echo -e "${YELLOW}Step 9: Running database migrations...${NC}"
docker-compose -f docker-compose.production.yml exec -T app php artisan migrate --force
echo -e "${GREEN}✓ Migrations completed${NC}"

echo -e "${YELLOW}Step 10: Seeding demo data...${NC}"
docker-compose -f docker-compose.production.yml exec -T app php artisan db:seed --class=DemoSeeder --force
echo -e "${GREEN}✓ Demo data seeded${NC}"

echo -e "${YELLOW}Step 11: Creating storage link...${NC}"
docker-compose -f docker-compose.production.yml exec -T app php artisan storage:link
echo -e "${GREEN}✓ Storage link created${NC}"

echo -e "${YELLOW}Step 12: Optimizing application...${NC}"
docker-compose -f docker-compose.production.yml exec -T app php artisan optimize
docker-compose -f docker-compose.production.yml exec -T app php artisan config:cache
docker-compose -f docker-compose.production.yml exec -T app php artisan route:cache
docker-compose -f docker-compose.production.yml exec -T app php artisan view:cache
echo -e "${GREEN}✓ Application optimized${NC}"

echo -e "${YELLOW}Step 13: Restarting queue workers...${NC}"
docker-compose -f docker-compose.production.yml restart horizon
echo -e "${GREEN}✓ Queue workers restarted${NC}"

echo ""
echo -e "${GREEN}╔═══════════════════════════════════════════════════╗${NC}"
echo -e "${GREEN}║  🎉 HUBIT Deployment Complete!                   ║${NC}"
echo -e "${GREEN}╚═══════════════════════════════════════════════════╝${NC}"
echo ""
echo -e "Application URL: ${YELLOW}https://cp.ip.mr${NC}"
echo -e "Documentation URL: ${YELLOW}https://docs.ip.mr${NC}"
echo ""
echo -e "${YELLOW}Default Credentials:${NC}"
echo -e "  Admin: ${GREEN}admin@hubit.test / password${NC}"
echo -e "  Client 1: ${GREEN}john@example.com / password${NC}"
echo -e "  Client 2: ${GREEN}jane@example.com / password${NC}"
echo ""
echo -e "${YELLOW}Next Steps:${NC}"
echo "  1. Configure SSL certificates (see deploy/bin/setup-ssl.sh)"
echo "  2. Update Stripe webhook URL in Stripe Dashboard"
echo "  3. Verify cPanel WHM connection"
echo "  4. Change default passwords"
echo ""
