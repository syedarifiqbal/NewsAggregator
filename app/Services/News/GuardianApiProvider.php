<?php

namespace App\Services\News;

use App\Contracts\NewsProviderInterface;
use App\DTOs\ArticleDTO;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GuardianApiProvider implements NewsProviderInterface{
    public function name(): string
    {
        return 'theGuardian';
    }

    public function fetch(string $keyword, int $page = 1): array
    {
        $url = config('services.guardianapi.url') . '/search';
        $api_key = config('services.guardianapi.key');


        $response = Http::retry(
                3,
                fn ($attempt) => $attempt * 1000,
                function (\Exception $e) {
                    
                    if ($e instanceof \Illuminate\Http\Client\RequestException) {
                        Log::error('GuardianAPI request failed', [
                            'status' => $e->response->status(),
                            'body' => $e->response->json(),
                        ]);
                    } else {
                        Log::error('GuardianAPI request failed', [
                            'message' => $e->getMessage(),
                        ]);
                    }
                    return true;
                }      
            )
            ->connectTimeout(3)
            ->timeout(10)
            ->acceptJson()
            ->get(
                $url,
                [
                    'page' => $page,
                    'api-key' => $api_key,
                ]
            );


        return collect(
            $response->json('response.results', [])
        )
            ->map(fn ($article) => new ArticleDTO(
                title: $article['webTitle'] ?? '',
                description: $article['sectionName'] ?? null,
                url: $article['webUrl'] ?? '',
                image: null,
                source: 'The Guardian',
                publishedAt: $article['webPublicationDate'] ?? ''
            ))
            ->all();
    }
}