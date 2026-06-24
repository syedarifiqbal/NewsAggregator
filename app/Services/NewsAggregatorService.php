<?php

namespace App\Services;

use App\Contracts\NewsProviderInterface;
use App\Services\News\GuardianApiProvider;
use App\Services\News\NewsApiProvider;
use Illuminate\Support\Facades\Log;

class NewsAggregatorService
{

    public function __construct(private iterable $providers) { }

    function fetchAll(string $keyword, int $page = 1): array
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
                // log error but don't break whole system
                Log::error('Provider failed', [
                    'provider' => $provider->name(),
                    'error' => $e->getMessage()
                ]);
                continue;
            }
        }

        return $this->normalize($allArticles);
    }

    private function normalize(array $items): array
    {
        return collect($items)
            ->sortByDesc(fn ($item) => $item['article']->publishedAt ?? null)
            ->values()
            ->all();
    }
}
