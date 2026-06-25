<?php

namespace App\Repositories;

use App\Contracts\CategroyRepositoryContract;
use Illuminate\Support\Facades\Cache;

/**
 * Decorator that wraps CategoryRepository with a Redis cache layer.
 * Follows the Decorator pattern — same contract, added caching behavior.
 * Cache is only busted when a new category is actually created.
 */
class CachedCategoryRepository implements CategroyRepositoryContract
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

        if ($result->wasRecentlyCreated) {
            Cache::forget('categories:all');
        }

        return $result;
    }
}
