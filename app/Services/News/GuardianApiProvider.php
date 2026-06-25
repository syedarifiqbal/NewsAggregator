<?php

namespace App\Services\News;

use App\Contracts\NewsProviderContract;
use App\DTOs\ArticleDTO;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GuardianApiProvider implements NewsProviderContract{
    public function name(): string
    {
        return 'theGuardian';
    }

    public function fetch(string $keyword, int $page = 1): array
    {
        $url = config('services.guardianapi.url') . '/search';
        $api_key = config('services.guardianapi.key');


        $response = Http::connectTimeout(3)
            ->timeout(10)
            ->acceptJson()
            ->get(
                $url,
                [
                    'page'          => $page,
                    'api-key'       => $api_key,
                    'show-tags'     => 'contributor',
                    'show-fields'   => 'thumbnail',
                ]
            );

        return collect(
            $response->json('response.results', [])
        )
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