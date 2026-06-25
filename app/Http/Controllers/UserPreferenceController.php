<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdatePreferenceRequest;
use App\Http\Resources\ArticleResource;
use App\Services\UserPreferenceService;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

#[OA\SecurityScheme(
    securityScheme: 'sanctum',
    type: 'http',
    scheme: 'bearer',
    bearerFormat: 'JWT'
)]
class UserPreferenceController extends Controller
{
    public function __construct(private UserPreferenceService $preferenceService) {}

    #[OA\Get(
        path: '/api/user/preferences',
        summary: 'Get current user preferences',
        security: [['sanctum' => []]],
        responses: [
            new OA\Response(response: 200, description: 'User preferences'),
            new OA\Response(response: 401, description: 'Unauthenticated'),
        ]
    )]
    public function show(Request $request)
    {
        $preferences = $this->preferenceService->getPreferences($request->user()->id);
        return $this->success($preferences);
    }

    #[OA\Put(
        path: '/api/user/preferences',
        summary: 'Update user preferences',
        security: [['sanctum' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'preferred_sources', type: 'array', items: new OA\Items(type: 'string'), example: '["The Guardian", "The New York Times"]'),
                    new OA\Property(property: 'preferred_categories', type: 'array', items: new OA\Items(type: 'string'), example: '["sport", "technology"]'),
                    new OA\Property(property: 'preferred_authors', type: 'array', items: new OA\Items(type: 'string'), example: '["Taha Hashim"]'),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Preferences updated successfully'),
            new OA\Response(response: 401, description: 'Unauthenticated'),
            new OA\Response(response: 422, description: 'Validation error'),
        ]
    )]
    public function update(UpdatePreferenceRequest $request)
    {
        $data = $this->preferenceService->updatePreferences(
            $request->user()->id,
            $request->validated()
        );

        return $this->success($data, 'Preferences updated successfully.');
    }

    #[OA\Get(
        path: '/api/user/feed',
        summary: 'Get personalized article feed based on user preferences',
        security: [['sanctum' => []]],
        parameters: [
            new OA\Parameter(name: 'filter[search]', in: 'query', required: false, schema: new OA\Schema(type: 'string')),
            new OA\Parameter(name: 'filter[published_from]', in: 'query', required: false, schema: new OA\Schema(type: 'string', format: 'date')),
            new OA\Parameter(name: 'filter[published_to]', in: 'query', required: false, schema: new OA\Schema(type: 'string', format: 'date')),
            new OA\Parameter(name: 'sort', in: 'query', required: false, schema: new OA\Schema(type: 'string', enum: ['published_at', '-published_at', 'title', '-title'])),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Personalized paginated list of articles'),
            new OA\Response(response: 401, description: 'Unauthenticated'),
        ]
    )]
    public function feed(Request $request)
    {
        $feeds = $this->preferenceService->personalizedFeed($request->user()->id);
        return ArticleResource::collection($feeds);
    }
}
