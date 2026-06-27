<?php

namespace App\Services\News;

use App\DTOs\ArticleDTO;

class GuardianApiProvider extends BaseProvider
{
    public function name(): string
    {
        return 'theGuardian';
    }

    public function fetch(string $keyword, int $page = 1): array
    {
        $response = $this->client()->get(
            config('services.guardianapi.url') . '/search',
            [
                'page' => $page,
                'api-key' => config('services.guardianapi.key'),
                'show-tags' => 'contributor',
                'show-fields' => 'thumbnail',
            ]
        );

        return collect($response->json('response.results', []))
            ->map(fn ($article) => new ArticleDTO(
                title: $article['webTitle'] ?? '',
                description: null,
                url: $article['webUrl'] ?? '',
                image: $article['fields']['thumbnail'] ?? null,
                source: 'The Guardian',
                publishedAt: $article['webPublicationDate'] ?? '',
                category: $article['sectionName'] ?? null,
                author: collect($article['tags'] ?? [])->firstWhere('type', 'contributor')['webTitle'] ?? null,
            ))
            ->all();
    }
}
