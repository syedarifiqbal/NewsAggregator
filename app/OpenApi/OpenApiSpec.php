<?php

namespace App\OpenApi;

use OpenApi\Attributes as OA;

#[OA\Info(
    title: 'News Aggregator API',
    version: '1.0.0',
    description: 'A news aggregation API that pulls articles from multiple providers and serves them with filtering, sorting, and personalized feeds.'
)]
#[OA\SecurityScheme(
    securityScheme: 'sanctum',
    type: 'http',
    scheme: 'bearer',
    bearerFormat: 'JWT'
)]
class OpenApiSpec {}
