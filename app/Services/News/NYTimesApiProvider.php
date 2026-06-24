<?php

namespace App\Services\News;

use App\Contracts\NewsProviderInterface;
use App\DTOs\ArticleDTO;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class NYTimesApiProvider implements NewsProviderInterface {
    
    public function name(): string
    {
        return 'NYTimes';
    }

    public function fetch(string $keyword, int $page = 1): array
    {
        $url = config('services.nytimesapi.url') . '/search/v2/articlesearch.json';
        $api_key = config('services.nytimesapi.key');

        $response = Http::retry(
                3,
                fn ($attempt) => $attempt * 1000,
                function (\Exception $e) {
                    
                    if ($e instanceof \Illuminate\Http\Client\RequestException) {
                        Log::error('NYTimesAPI request failed', [
                            'status' => $e->response->status(),
                            'body' => $e->response->json(),
                        ]);
                    } else {
                        Log::error('NYTimesAPI request failed', [
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
                    /**
                     * Not sure what keyword to use here, so I will comment it out for now.
                     * We may store the user's query in the database and use it here to fetch news based on their interests.
                     */
                    // 'q' => $keyword,
                    'page' => $page - 1,
                    'sortBy' => 'newest',
                    'api-key' => $api_key,
                ]
            );


        return collect(
            $response->json('response.docs', [])
        )
            ->map(fn ($article) => new ArticleDTO(
                title: $article['headline']['main'] ?? '',
                description: $article['abstract'] ?? null,
                url: $article['web_url'] ?? '',
                image: isset($article['multimedia'][0]['url'])
                    ? 'https://www.nytimes.com/' . $article['multimedia'][0]['url']
                    : null,
                source: $article['source'] ?? 'The New York Times',
                publishedAt: $article['pub_date'] ?? ''
            ))
            ->all();
    }
}