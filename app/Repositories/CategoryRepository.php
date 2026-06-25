<?php

namespace App\Repositories;

use App\Contracts\CategroyRepositoryContract;
use App\Models\Category;

class CategoryRepository implements CategroyRepositoryContract
{
    public function __construct(private Category $model, private int $ttl = 3600) { }

    public function all()
    {
        return $this->model->all();
    }

    public function find($id)
    {
        return $this->model->find($id);
    }

    public function findBySlug($slug)
    {
        return $this->model->where('slug', $slug)->first();
    }

    public function firstOrCreate($attributes)
    {
        return $this->model->firstOrCreate($attributes);
    }
}
