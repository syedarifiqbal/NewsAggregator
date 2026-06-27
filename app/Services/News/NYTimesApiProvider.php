<?php

namespace App\Services\News;

use App\DTOs\ArticleDTO;

class NYTimesApiProvider extends BaseProvider
{
    public function name(): string
    {
        return 'NYTimes';
    }

    public function fetch(string $keyword, int $page = 1): array
    {
        $response = $this->client()->get(
            config('services.nytimesapi.url') . '/search/v2/articlesearch.json',
            [
                'page' => $page - 1,
                'sortBy' => 'newest',
                'api-key' => config('services.nytimesapi.key'),
            ]
        );

        return collect($response->json('response.docs', []))
            ->map(fn ($article) => new ArticleDTO(
                title: $article['headline']['main'] ?? '',
                description: $article['abstract'] ?? null,
                url: $article['web_url'] ?? '',
                image: isset($article['multimedia'][0]['url'])
                    ? 'https://www.nytimes.com/' . $article['multimedia'][0]['url']
                    : null,
                source: $article['source'] ?? 'The New York Times',
                publishedAt: $article['pub_date'] ?? '',
                category: $article['section_name'] ?? null,
                author: $article['byline']['original'] ?? null,
            ))
            ->all();
    }
}
