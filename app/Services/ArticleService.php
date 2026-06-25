<?php

namespace App\Services;

use App\Contracts\ArticleRepositoryContract;
use Illuminate\Pagination\LengthAwarePaginator;

class ArticleService
{
    public function __construct(private ArticleRepositoryContract $articleRepo) {}

    function index(): LengthAwarePaginator
    {
        return $this->articleRepo->index();
    }

    function personalizedFeed(array $preferences): LengthAwarePaginator
    {
        return $this->articleRepo->personalizedFeed($preferences);
    }
}
