#!/bin/bash
set -e

echo "==> Setting up News Aggregator..."

# Copy .env if it doesn't exist
if [ ! -f .env ]; then
    cp .env.example .env
    echo "==> .env file created from .env.example"
fi

# Build and start containers
echo "==> Building Docker containers..."
docker compose build

echo "==> Starting containers..."
docker compose up -d

# Wait for containers to be ready
echo "==> Waiting for services to be ready..."
sleep 5

# Install dependencies
echo "==> Installing Composer dependencies..."
docker compose exec app composer install

# Generate app key if not set
echo "==> Generating application key..."
docker compose exec app php artisan key:generate

# Run migrations
echo "==> Running database migrations..."
docker compose exec app php artisan migrate

# Set storage permissions
echo "==> Setting storage permissions..."
docker compose exec app chmod -R 775 storage bootstrap/cache

echo ""
echo "============================================"
echo "  News Aggregator is ready!"
echo "  URL: http://localhost:8088"
echo "============================================"
