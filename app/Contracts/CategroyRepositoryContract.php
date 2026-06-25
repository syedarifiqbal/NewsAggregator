<?php

namespace App\Contracts;

interface CategroyRepositoryContract
{
    public function all();
    public function find($id);
    public function findBySlug($slug);
    public function firstOrCreate($attributes);
}
