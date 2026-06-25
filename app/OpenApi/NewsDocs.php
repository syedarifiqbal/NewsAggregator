<?php

namespace App\OpenApi;

use OpenApi\Attributes as OA;

class NewsDocs
{
    #[OA\Get(
        path: '/api/news',
        summary: 'List articles',
        tags: ['News'],
        parameters: [
            new OA\Parameter(name: 'filter[search]', in: 'query', required: false, description: 'Search title and description', schema: new OA\Schema(type: 'string')),
            new OA\Parameter(name: 'filter[source]', in: 'query', required: false, description: 'Exact match on source name', schema: new OA\Schema(type: 'string')),
            new OA\Parameter(name: 'filter[provider]', in: 'query', required: false, description: 'Exact match on provider', schema: new OA\Schema(type: 'string', enum: ['NewsAPI', 'theGuardian', 'NYTimes'])),
            new OA\Parameter(name: 'filter[category]', in: 'query', required: false, description: 'Partial match on category slug', schema: new OA\Schema(type: 'string')),
            new OA\Parameter(name: 'filter[author]', in: 'query', required: false, description: 'Partial match on author name', schema: new OA\Schema(type: 'string')),
            new OA\Parameter(name: 'filter[published_from]', in: 'query', required: false, description: 'Articles from this date', schema: new OA\Schema(type: 'string', format: 'date')),
            new OA\Parameter(name: 'filter[published_to]', in: 'query', required: false, description: 'Articles until this date', schema: new OA\Schema(type: 'string', format: 'date')),
            new OA\Parameter(name: 'sort', in: 'query', required: false, description: 'Sort field (prefix with - for descending)', schema: new OA\Schema(type: 'string', enum: ['published_at', '-published_at', 'title', '-title'])),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Paginated list of articles'),
        ]
    )]
    public function index() {}
}
