<?php

namespace App\Services;

use App\Contracts\ArticleRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

class ArticleService
{
    public function __construct(private ArticleRepositoryInterface $articleRepo) {}

    function index(): LengthAwarePaginator
    {
        return $this->articleRepo->index();
    }

    function personalizedFeed(array $preferences): LengthAwarePaginator
    {
        return $this->articleRepo->personalizedFeed($preferences);
    }
}
