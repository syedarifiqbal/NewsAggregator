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
        'author',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function scopePublishedFrom($query, $date)
    {
        return $query->where('published_at', '>=', $date);
    }

    public function scopeTitleSearch($query, $value)
    {
        return $query->where(function ($q) use ($value) {
            $q->where('title', 'ILIKE', "%{$value}%")
              ->orWhere('description', 'ILIKE', "%{$value}%");
        });
    }

    public function scopePublishedTo($query, $date)
    {
        return $query->where('published_at', '<=', $date);
    }

    public function scopeCategorySearch($query, $value)
    {
        return $query->whereHas('category', function ($q) use ($value) {
            $q->where('slug', 'ILIKE', "%{$value}%");
        });
    }

}
