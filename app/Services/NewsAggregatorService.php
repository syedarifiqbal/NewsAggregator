<?php

namespace App\Services;

use App\Contracts\ArticleRepositoryInterface;
use App\Contracts\CategroyRepositoryInterface;
use App\Exceptions\CircuitBreakerOpenException;
use App\Services\Resilience\RedisCircuitBreaker;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class NewsAggregatorService
{
    public function __construct(
        private iterable $providers,
        private ArticleRepositoryInterface $articleRepo,
        private CategroyRepositoryInterface $categoryRepo,
    ) { }

    function fetchAndStore(string $keyword = 'tech', int $page = 1): array
    {
        $articles = $this->fetchAll($keyword, $page);

        foreach ($articles as $item) {
            $dto = $item['article'];
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

    protected function fetchAll(string $keyword, int $page = 1): array
    {
        $allArticles = [];

        foreach ($this->providers as $provider) {
            try {
                $breaker = new RedisCircuitBreaker($provider->name());

                $articles = $breaker->execute(
                    fn () => $provider->fetch($keyword, $page),
                    fn () => []
                );

                foreach ($articles as $article) {
                    $allArticles[] = [
                        'provider' => $provider->name(),
                        'article' => $article
                    ];
                }

            } catch (CircuitBreakerOpenException $e) {
                Log::warning('Circuit breaker open', [
                    'provider' => $provider->name(),
                ]);
                continue;
            } catch (\Throwable $e) {
                Log::error('Provider failed', [
                    'provider' => $provider->name(),
                    'error' => $e->getMessage()
                ]);
                continue;
            }
        }

        return $this->normalize($allArticles);
    }

    protected function normalize(array $items): array
    {
        return collect($items)
            ->sortByDesc(fn ($item) => $item['article']->publishedAt ?? null)
            ->values()
            ->all();
    }
}
