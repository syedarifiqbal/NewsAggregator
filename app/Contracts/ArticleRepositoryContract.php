<?php

namespace App\Contracts;

use App\DTOs\ArticleDTO;
use App\Models\Article;
use Illuminate\Pagination\LengthAwarePaginator;

interface ArticleRepositoryContract
{
    public function index(): LengthAwarePaginator;
    public function personalizedFeed(array $preferences): LengthAwarePaginator;
    public function updateOrCreate(ArticleDTO $dto, string $provider, ?int $categoryId): Article;
}
