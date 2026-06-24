<?php

namespace App\Repositories;

use App\Contracts\CategroyRepositoryInterface;
use Illuminate\Support\Facades\Cache;

class CachedCategoryRepository implements CategroyRepositoryInterface
{
    public function __construct(
        private CategoryRepository $repository,
        private int $ttl = 3600
    ) {}

    public function all()
    {
        return Cache::remember('categories:all', $this->ttl, fn() => $this->repository->all());
    }

    public function find($id)
    {
        return Cache::remember("categories:{$id}", $this->ttl, fn() => $this->repository->find($id));
    }

    public function findBySlug($slug)
    {
        return Cache::remember("categories:slug:{$slug}", $this->ttl, fn() => $this->repository->findBySlug($slug));
    }

    public function firstOrCreate($attributes)
    {
        $result = $this->repository->firstOrCreate($attributes);
        // check if it is created and if so, clear the cache for all categories
        if ($result->wasRecentlyCreated) {
            Cache::forget('categories:all');
        }
        return $result;
    }
}
