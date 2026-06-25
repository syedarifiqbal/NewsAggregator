<?php

namespace App\Http\Controllers;

use App\Http\Resources\ArticleResource;
use App\Services\ArticleService;

class NewsController extends Controller
{
    function __construct(private ArticleService $articleService) {}

    public function index()
    {
        return ArticleResource::collection($this->articleService->index());
    }
}
