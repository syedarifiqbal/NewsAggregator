<?php

namespace App\Services\News;

use App\Contracts\NewsProviderInterface;
use App\DTOs\ArticleDTO;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class NewsApiProvider implements NewsProviderInterface {
    
    public function name(): string
    {
        return 'NewsAPI';
    }

    public function fetch(string $keyword, int $page = 1): array
    {
        $url = config('services.newsapi.url') . '/top-headlines';
        $api_key = config('services.newsapi.key');

        $response = Http::connectTimeout(3)
            ->timeout(10)
            ->acceptJson()
            ->get(
                $url,
                [
                    /**
                     * Not sure what keyword to use here, so I will comment it out for now.
                     * We may store the user's query in the database and use it here to fetch news based on their interests.
                     */
                    // 'q' => $keyword,
                    'page' => $page,
                    // since I don't know what keyword/topic/cagtegory to use, I will use country instead. we may take user's country while registering and use it here to fetch news from their country
                    'country' => 'us', 
                    'sortBy' => 'popularity',
                    'apiKey' => $api_key,
                ]
            );

        return collect(
            $response->json('articles', [])
        )
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