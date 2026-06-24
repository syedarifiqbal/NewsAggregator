<?php

namespace App\Repositories;

use App\Contracts\ArticleRepositoryInterface;
use App\DTOs\ArticleDTO;
use App\Models\Article;
use Illuminate\Pagination\LengthAwarePaginator;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class ArticleRepository implements ArticleRepositoryInterface
{
    public function __construct(private Article $model) {}
    public function updateOrCreate(ArticleDTO $dto, string $provider, ?int $categoryId): Article
    {
        return $this->model->updateOrCreate(
            [
                'url' => $dto->url,
            ],
            [
                'title' => $dto->title,
                'description' => $dto->description,
                'image_url' => $dto->image,
                'source' => $dto->source,
                'provider' => $provider,
                'category_id' => $categoryId,
                'published_at' => $dto->publishedAt,
            ]
        );
    }
}
