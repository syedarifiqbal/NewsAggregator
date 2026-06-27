<?php

namespace App\Services;

use App\Contracts\ArticleRepositoryContract;
use App\Contracts\CategroyRepositoryContract;
use App\Exceptions\CircuitBreakerOpenException;
use App\Services\Resilience\RedisCircuitBreaker;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * Orchestrates fetching articles from all registered news providers
 * and persisting them to the database.
 *
 * Used by the scheduled FetchArticlesCommand (runs everyFiveMinutes).
 *
 * Each provider call is wrapped in a CircuitBreaker to prevent
 * hammering a failing API — if one provider is down, the others
 * continue unaffected.
 */
class NewsAggregatorService
{
    public function __construct(
        private iterable $providers,
        private ArticleRepositoryContract $articleRepo,
        private CategroyRepositoryContract $categoryRepo,
    ) { }

    /**
     * Fetch articles from all providers and store them in the database.
     * Uses fetchAll() for retrieval, then persists each article.
     * Duplicate articles are detected by URL and updated in place.
     */
    function store(string $keyword = 'tech', int $page = 1): array
    {
        $articles = $this->fetchAll($keyword, $page);

        foreach ($articles as $item) {
            $dto = $item['article'];

            // Find or create the category from the provider's category string
            $category = $dto->category
                ? $this->categoryRepo->firstOrCreate([
                    'name' => $dto->category,
                    'slug' => Str::slug($dto->category),
                ])
                : null;

            $this->articleRepo->updateOrCreate(
                $dto,
                $item['provider'],
                $category?->id
            );
        }

        return $articles;
    }

    protected function fetchAll(string $keyword, int $page = 1, int $maxPages = 5): array
    {
        $allArticles = [];

        foreach ($this->providers as $provider) {
            $articles = $this->fetchFromProvider($provider, $keyword, $page, $maxPages);
            array_push($allArticles, ...$articles);
        }

        return $this->normalize($allArticles);
    }

    /**
     * Paginate a single provider with circuit-breaker protection.
     * Stops early on empty page, open circuit, or any provider error.
     */
    protected function fetchFromProvider($provider, string $keyword, int $page, int $maxPages): array
    {
        $breaker = new RedisCircuitBreaker($provider->name());
        $articles = [];

        for ($currentPage = $page; $currentPage < $page + $maxPages; $currentPage++) {
            try {
                $page_articles = $breaker->execute(
                    fn () => $provider->fetch($keyword, $currentPage),
                    fn () => []
                );

                if (empty($page_articles)) {
                    break;
                }

                foreach ($page_articles as $article) {
                    $articles[] = ['provider' => $provider->name(), 'article' => $article];
                }

            } catch (CircuitBreakerOpenException $e) {
                Log::warning('Circuit breaker open', ['provider' => $provider->name()]);
                break;
            } catch (\Throwable $e) {
                Log::error('Provider failed', [
                    'provider' => $provider->name(),
                    'page' => $currentPage,
                    'error' => $e->getMessage(),
                ]);
                break;
            }
        }

        return $articles;
    }

    /**
     * Sort articles by publish date (newest first) across all providers.
     */
    protected function normalize(array $items): array
    {
        return collect($items)
            ->sortByDesc(fn ($item) => $item['article']->publishedAt ?? null)
            ->values()
            ->all();
    }
}
