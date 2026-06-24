<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    protected $fillable = [
        'title',
        'description',
        'image',
        'url',
        'source',
        'provider',
        'category_id',
        'published_at',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
