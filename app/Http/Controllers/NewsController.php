<?php

namespace App\Http\Controllers;

use App\Http\Resources\ArticleResource;
use App\Services\ArticleService;
use OpenApi\Attributes as OA;

#[OA\Info(title: 'News Aggregator API', version: '1.0.0')]
class NewsController extends Controller
{
    function __construct(private ArticleService $articleService) {}

    #[OA\Get(
        path: '/api/news',
        summary: 'List articles',
        parameters: [
            new OA\Parameter(name: 'filter[title]', in: 'query', required: false, schema: new OA\Schema(type: 'string')),
            new OA\Parameter(name: 'filter[source]', in: 'query', required: false, schema: new OA\Schema(type: 'string')),
            new OA\Parameter(name: 'filter[provider]', in: 'query', required: false, schema: new OA\Schema(type: 'string')),
            new OA\Parameter(name: 'filter[category]', in: 'query', required: false, schema: new OA\Schema(type: 'string')),
            new OA\Parameter(name: 'filter[published_from]', in: 'query', required: false, schema: new OA\Schema(type: 'string', format: 'date')),
            new OA\Parameter(name: 'filter[published_to]', in: 'query', required: false, schema: new OA\Schema(type: 'string', format: 'date')),
            new OA\Parameter(name: 'sort', in: 'query', required: false, schema: new OA\Schema(type: 'string', enum: ['published_at', '-published_at', 'title', '-title'])),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Paginated list of articles'),
        ]
    )]
    public function index()
    {
        return ArticleResource::collection($this->articleService->index());
    }
}
