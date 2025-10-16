#!/bin/bash

set -e

echo "🔒 Setting up SSL Certificates for HUBIT"

GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m' # No Color

if [ "$EUID" -ne 0 ]; then 
    echo -e "${RED}Please run as root or with sudo${NC}"
    exit 1
fi

echo -e "${YELLOW}Installing certbot...${NC}"
apt-get update
apt-get install -y certbot
echo -e "${GREEN}✓ Certbot installed${NC}"

echo -e "${YELLOW}Setting up SSL for cp.ip.mr...${NC}"
certbot certonly --standalone \
    --agree-tos \
    --no-eff-email \
    --email admin@ip.mr \
    -d cp.ip.mr \
    --non-interactive

mkdir -p /home/ubuntu/repos/HUBIT/deploy/ssl/cp.ip.mr
cp /etc/letsencrypt/live/cp.ip.mr/fullchain.pem /home/ubuntu/repos/HUBIT/deploy/ssl/cp.ip.mr/
cp /etc/letsencrypt/live/cp.ip.mr/privkey.pem /home/ubuntu/repos/HUBIT/deploy/ssl/cp.ip.mr/
echo -e "${GREEN}✓ SSL certificate for cp.ip.mr installed${NC}"

echo -e "${YELLOW}Setting up SSL for docs.ip.mr...${NC}"
certbot certonly --standalone \
    --agree-tos \
    --no-eff-email \
    --email admin@ip.mr \
    -d docs.ip.mr \
    --non-interactive

mkdir -p /home/ubuntu/repos/HUBIT/deploy/ssl/docs.ip.mr
cp /etc/letsencrypt/live/docs.ip.mr/fullchain.pem /home/ubuntu/repos/HUBIT/deploy/ssl/docs.ip.mr/
cp /etc/letsencrypt/live/docs.ip.mr/privkey.pem /home/ubuntu/repos/HUBIT/deploy/ssl/docs.ip.mr/
echo -e "${GREEN}✓ SSL certificate for docs.ip.mr installed${NC}"

echo -e "${YELLOW}Setting up auto-renewal...${NC}"
echo "0 3 * * * certbot renew --quiet && systemctl reload nginx" | crontab -
echo -e "${GREEN}✓ Auto-renewal configured${NC}"

echo ""
echo -e "${GREEN}╔═══════════════════════════════════════════════════╗${NC}"
echo -e "${GREEN}║  🔒 SSL Setup Complete!                          ║${NC}"
echo -e "${GREEN}╚═══════════════════════════════════════════════════╝${NC}"
echo ""
echo -e "Certificates installed for:"
echo -e "  • ${GREEN}cp.ip.mr${NC}"
echo -e "  • ${GREEN}docs.ip.mr${NC}"
echo ""
echo -e "${YELLOW}Next Steps:${NC}"
echo "  1. Restart nginx: docker-compose -f docker-compose.production.yml restart nginx"
echo "  2. Verify SSL: curl https://cp.ip.mr"
echo ""
