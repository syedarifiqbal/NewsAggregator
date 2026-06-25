# News Aggregator

A Laravel-based news aggregation application that pulls articles from multiple news providers (NewsAPI, The Guardian, NY Times), stores them in a unified format, and exposes a filterable, paginated REST API. Articles are automatically fetched and updated via a scheduled command.

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

### API Keys

Register for free API keys at:

- **NewsAPI** — https://newsapi.org/register
- **The Guardian** — https://open-platform.theguardian.com/access
- **NY Times** — https://developer.nytimes.com/accounts/create

Add them to your `.env` file:

| Variable | Description |
|---|---|
| `NEWSAPI_KEY` | Your NewsAPI.org API key |
| `GUARDIAN_KEY` | Your Guardian Open Platform API key |
| `NYTIMESAPI_KEY` | Your NY Times Article Search API key |

### Start / Stop

```bash
docker compose up -d     # start
docker compose down      # stop
```

### Fetch Articles

Articles are fetched automatically every hour by the scheduler container. To fetch manually:

```bash
docker compose exec app php artisan articles:fetch
```

## Docker Services

| Service   | Description | Host URL / Port |
|-----------|-------------|-----------------|
| App       | PHP-FPM application server | — |
| Nginx     | Reverse proxy | http://localhost:8088 |
| PostgreSQL| Database | localhost:5442 |
| Redis     | Cache, sessions, queues | localhost:6389 |
| Scheduler | Runs `articles:fetch` every hour | — |

Ports are non-default to avoid conflicts with other local projects.

## API Documentation

Swagger UI is available at: **http://localhost:8088/api/docs**

Regenerate docs after changes:

```bash
docker compose exec app php artisan l5-swagger:generate
```

## API Endpoints

### `GET /api/news`

Returns a paginated list of articles with filtering, sorting, and rate limiting (60 requests/minute).

#### Filtering

| Parameter | Type | Description |
|---|---|---|
| `filter[search]` | string | Case-insensitive partial match on title and description |
| `filter[source]` | string | Exact match on source name (e.g. `The Guardian`) |
| `filter[provider]` | string | Exact match on provider (`NewsAPI`, `theGuardian`, `NYTimes`) |
| `filter[category]` | string | Case-insensitive partial match on category slug |
| `filter[author]` | string | Case-insensitive partial match on author name |
| `filter[published_from]` | date | Articles published on or after this date |
| `filter[published_to]` | date | Articles published on or before this date |

#### Sorting

| Parameter | Description |
|---|---|
| `sort=published_at` | Sort by publish date ascending |
| `sort=-published_at` | Sort by publish date descending (default) |
| `sort=title` | Sort by title ascending |
| `sort=-title` | Sort by title descending |

#### Example Requests

```
GET /api/news
GET /api/news?filter[search]=climate&sort=-published_at
GET /api/news?filter[provider]=NYTimes&filter[category]=sport
GET /api/news?filter[author]=barney&filter[source]=The Guardian
GET /api/news?filter[published_from]=2026-06-01&filter[published_to]=2026-06-25&sort=title
```

#### Example Response

```json
{
  "data": [
    {
      "id": 1,
      "title": "Article title here",
      "description": "Brief description...",
      "url": "https://example.com/article",
      "image": "https://example.com/image.jpg",
      "source": "The Guardian",
      "provider": "theGuardian",
      "category": "Sport",
      "author": "Barney Ronay",
      "published_at": "2026-06-24T12:00:00Z"
    }
  ],
  "links": { "first": "...", "last": "...", "prev": null, "next": "..." },
  "meta": { "current_page": 1, "last_page": 5, "per_page": 15, "total": 72 }
}
```

## Architecture

### Design Patterns & SOLID Principles

- **Repository Pattern** — Data access is abstracted behind interfaces (`ArticleRepositoryInterface`, `CategoryRepositoryInterface`), keeping controllers and services decoupled from Eloquent.
- **Decorator Pattern** — `CachedCategoryRepository` wraps `CategoryRepository` to add a Redis cache layer without modifying the original class (Single Responsibility / Open-Closed).
- **Provider Pattern** — Each news source implements `NewsProviderInterface`, making it easy to add new sources without modifying existing code (Open-Closed).
- **Service Layer** — `ArticleService` handles reading from the database. `NewsAggregatorService` orchestrates fetching from external APIs and persisting through repositories (Single Responsibility).
- **DTO (Data Transfer Object)** — `ArticleDTO` normalizes data from different API response formats into a unified structure before persistence.
- **Dependency Injection** — All services and repositories are resolved through Laravel's service container via interface bindings (Dependency Inversion).
- **Circuit Breaker Pattern** — `RedisCircuitBreaker` prevents cascading failures by short-circuiting requests to failing providers after repeated errors, with automatic recovery.

### Project Structure

```
app/
├── Console/
│   └── Commands/
│       └── FetchArticlesCommand         # Artisan command for scheduled fetching
├── Contracts/                           # Interfaces
│   ├── ArticleRepositoryInterface
│   ├── CategoryRepositoryInterface
│   ├── CircuitBreakerInterface
│   └── NewsProviderInterface
├── DTOs/
│   └── ArticleDTO                       # Unified article data structure
├── Exceptions/
│   └── CircuitBreakerOpenException
├── Http/
│   ├── Controllers/
│   │   └── NewsController               # API endpoints
│   └── Resources/
│       └── ArticleResource              # API response transformation
├── Models/
│   ├── Article                          # With scopes for filtering
│   └── Category
├── Providers/
│   ├── NewsAggregatorServiceProvider    # Binds news providers via tagging
│   └── RepositoryServiceProvider        # Binds repository interfaces
├── Repositories/
│   ├── ArticleRepository                # Spatie QueryBuilder filtering
│   ├── CategoryRepository               # Eloquent implementation
│   └── CachedCategoryRepository         # Redis cache decorator
└── Services/
    ├── ArticleService                   # Read articles from DB
    ├── NewsAggregatorService            # Fetch from APIs + store to DB
    ├── Resilience/
    │   └── RedisCircuitBreaker          # Circuit breaker implementation
    └── News/
        ├── NewsApiProvider              # NewsAPI.org integration
        ├── GuardianApiProvider          # The Guardian integration
        └── NYTimesApiProvider           # NY Times integration
```

### Adding a New News Provider

1. Create a new class in `app/Services/News/` implementing `NewsProviderInterface`
2. Map the API response fields to `ArticleDTO`
3. Register the provider in `NewsAggregatorServiceProvider`
4. Add API config to `config/services.php` and `.env.example`

No existing code needs to be modified.

## Tech Stack

- **PHP 8.4** (FPM)
- **Laravel 13**
- **Nginx** (reverse proxy)
- **PostgreSQL 16** (database)
- **Redis 7** (cache, sessions, queues)
- **[Spatie Laravel Query Builder](https://spatie.be/docs/laravel-query-builder)** (API filtering & sorting)
- **[L5-Swagger](https://github.com/DarkaOnLine/L5-Swagger)** (API documentation)
- **Docker Compose** (containerized development environment)

## Useful Commands

```bash
# Fetch articles manually
docker compose exec app php artisan articles:fetch

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

# View scheduler logs
docker compose logs -f scheduler

# Generate Swagger docs
docker compose exec app php artisan l5-swagger:generate

# Run any artisan command (requires make)
make artisan cmd="route:list"
```
