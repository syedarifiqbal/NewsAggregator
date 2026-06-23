# News Aggregator

A Laravel application with a Dockerized development environment.

## Prerequisites

- [Docker](https://docs.docker.com/get-docker/) and Docker Compose

## Getting Started

### First-time setup

```bash
git clone https://github.com/syedarifiqbal/NewsAggregator.git
cd NewsAggregator
cp .env.example .env
docker compose build
docker compose up -d
docker compose exec app composer install
docker compose exec app php artisan key:generate
docker compose exec app php artisan migrate
```

Or if you have `make` installed:

```bash
make setup
```

### Start / Stop

```bash
docker compose up -d
docker compose down
```

## Services

| Service    | Host URL / Port          |
|------------|--------------------------|
| App        | http://localhost:8088     |
| PostgreSQL | localhost:5442           |
| Redis      | localhost:6389           |

## Useful Commands

```bash
# Run migrations
docker compose exec app php artisan migrate

# Fresh migrate with seeders
docker compose exec app php artisan migrate:fresh --seed

# Open a shell in the app container
docker compose exec app bash

# Run tests
docker compose exec app php artisan test

# View logs
docker compose logs -f
```

## Tech Stack

- PHP 8.4 (FPM)
- Laravel 13
- Nginx
- PostgreSQL 16
- Redis 7
