<?php

namespace App\Contracts;

interface CategroyRepositoryInterface
{
    public function all();
    public function find($id);
    public function findBySlug($slug);
    public function firstOrCreate($attributes);
}
