<?php

namespace App\Contracts;

use App\DTOs\ArticleDTO;
use App\Models\Article;
use Illuminate\Pagination\LengthAwarePaginator;

interface ArticleRepositoryInterface
{
    public function index(): LengthAwarePaginator;
    public function updateOrCreate(ArticleDTO $dto, string $provider, ?int $categoryId): Article;
}
