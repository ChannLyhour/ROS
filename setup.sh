#!/bin/bash

# Colors for output
GREEN='\033[0;32m'
BLUE='\033[0;34m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

echo -e "${BLUE}=== ROS POS - Docker Setup ===${NC}\n"

# Check if Docker is installed
if ! command -v docker &> /dev/null; then
    echo -e "${YELLOW}⚠️  Docker is not installed. Please install Docker Desktop first.${NC}"
    echo "Download from: https://www.docker.com/products/docker-desktop"
    exit 1
fi

echo -e "${GREEN}✓ Docker is installed${NC}\n"

# Build images
echo -e "${BLUE}Building Docker images...${NC}"
docker-compose build

# Start containers
echo -e "\n${BLUE}Starting containers...${NC}"
docker-compose up -d

# Wait for database to be ready
echo -e "\n${BLUE}Waiting for database to be ready...${NC}"
sleep 10

# Copy env file
if [ ! -f .env ]; then
    echo -e "${BLUE}Setting up environment file...${NC}"
    cp .env.docker .env
fi

# Install dependencies
echo -e "\n${BLUE}Installing PHP dependencies...${NC}"
docker-compose exec -T app composer install

# Generate app key
echo -e "${BLUE}Generating application key...${NC}"
docker-compose exec -T app php artisan key:generate

# Run migrations and seed
echo -e "${BLUE}Running database migrations...${NC}"
docker-compose exec -T app php artisan migrate --force

echo -e "${BLUE}Seeding database...${NC}"
docker-compose exec -T app php artisan db:seed --force

# Install npm dependencies
echo -e "${BLUE}Installing npm dependencies...${NC}"
docker-compose exec -T app npm install

# Build assets
echo -e "${BLUE}Building frontend assets...${NC}"
docker-compose exec -T app npm run build

echo -e "\n${GREEN}✓ Setup complete!${NC}\n"
echo -e "${BLUE}Your application is running at:${NC}"
echo -e "  ${GREEN}http://localhost${NC}\n"
echo -e "${BLUE}Database credentials:${NC}"
echo -e "  Host: ${GREEN}localhost${NC}"
echo -e "  Port: ${GREEN}3306${NC}"
echo -e "  User: ${GREEN}ros_user${NC}"
echo -e "  Password: ${GREEN}ros_password${NC}\n"
echo -e "${BLUE}Useful commands:${NC}"
echo -e "  ${GREEN}make logs${NC}       - View container logs"
echo -e "  ${GREEN}make bash${NC}       - Access app container"
echo -e "  ${GREEN}make migrate${NC}    - Run migrations"
echo -e "  ${GREEN}make seed${NC}       - Seed database"
echo -e "  ${GREEN}make down${NC}       - Stop containers"
echo -e "  ${GREEN}make help${NC}       - View all commands\n"
