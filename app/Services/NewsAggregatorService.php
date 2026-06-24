<?php

namespace App\Services;

use App\Contracts\ArticleRepositoryInterface;
use App\Contracts\CategroyRepositoryInterface;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class NewsAggregatorService
{
    public function __construct(
        private iterable $providers,
        private ArticleRepositoryInterface $articleRepo,
        private CategroyRepositoryInterface $categoryRepo,
    ) { }


    function fetchAndStore(string $keyword, int $page = 1): array
    {
        $allArticles = [];

        foreach ($this->providers as $provider) {
            try {
                $articles = $provider->fetch($keyword, $page);

                foreach ($articles as $dto) {
                    $category = $dto->category
                        ? $this->categoryRepo->firstOrCreate([
                            'name' => $dto->category,
                            'slug' => Str::slug($dto->category),
                        ])
                        : null;

                    $article = $this->articleRepo->updateOrCreate(
                        $dto,
                        $provider->name(),
                        $category?->id
                    );

                    $allArticles[] = [
                        'provider' => $provider->name(),
                        'article' => $article
                    ];
                }

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

    protected function fetchAll(string $keyword, int $page = 1): array
    {
        $allArticles = [];

        foreach ($this->providers as $provider) {
            try {
                $articles = $provider->fetch($keyword, $page);

                foreach ($articles as $article) {
                    $allArticles[] = [
                        'provider' => $provider->name(),
                        'article' => $article
                    ];
                }

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
