<?php

namespace App\Repositories;

use App\Contracts\ArticleRepositoryContract;
use App\DTOs\ArticleDTO;
use App\Models\Article;
use Illuminate\Pagination\LengthAwarePaginator;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class ArticleRepository implements ArticleRepositoryContract
{
    public function __construct(private Article $model) {}

    public function index(): LengthAwarePaginator
    {
        return QueryBuilder::for(Article::class)
            ->allowedFilters(
                AllowedFilter::scope('search', 'titleSearch'),
                AllowedFilter::exact('source'),
                AllowedFilter::exact('provider'),
                AllowedFilter::scope('category', 'categorySearch'),
                AllowedFilter::scope('published_from'),
                AllowedFilter::scope('published_to'),
                AllowedFilter::scope('author', 'authorSearch'),
            )
            ->allowedSorts('published_at', 'title')
            ->defaultSort('-published_at')
            ->with('category:id,name')
            ->paginate();
    }

    public function personalizedFeed(array $preferences): LengthAwarePaginator
    {
        return QueryBuilder::for(
                Article::query()->forPreferences($preferences)
            )
            ->allowedFilters(
                AllowedFilter::scope('search', 'titleSearch'),
                AllowedFilter::scope('published_from'),
                AllowedFilter::scope('published_to'),
            )
            ->allowedSorts('published_at', 'title')
            ->defaultSort('-published_at')
            ->with('category:id,name')
            ->paginate();
    }

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
                'author' => $dto->author,
            ]
        );
    }
}
