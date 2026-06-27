<?php

namespace App\Services\News;

use App\DTOs\ArticleDTO;

class NewsApiProvider extends BaseProvider
{
    public function name(): string
    {
        return 'NewsAPI';
    }

    public function fetch(string $keyword, int $page = 1): array
    {
        $response = $this->client()->get(
            config('services.newsapi.url') . '/top-headlines',
            [
                'page' => $page,
                'country' => 'us',
                'sortBy' => 'popularity',
                'apiKey' => config('services.newsapi.key'),
            ]
        );

        return collect($response->json('articles', []))
            ->map(fn ($article) => new ArticleDTO(
                title: $article['title'] ?? '',
                description: $article['description'] ?? null,
                url: $article['url'] ?? '',
                image: $article['urlToImage'] ?? null,
                source: $article['source']['name'] ?? '',
                publishedAt: $article['publishedAt'] ?? '',
                category: $article['category'] ?? null,
                author: $article['author'] ?? null,
            ))
            ->all();
    }
}
