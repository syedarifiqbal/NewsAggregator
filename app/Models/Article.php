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

    public function scopeAuthorSearch($query, $value)
    {
        return $query->where('author', 'ILIKE', "%{$value}%");
    }

    public function scopeForPreferences($query, array $preferences)
    {
        return $query->where(function ($q) use ($preferences) {
            if (!empty($preferences['preferred_sources'])) {
                $q->orWhereIn('source', $preferences['preferred_sources']);
            }
            if (!empty($preferences['preferred_categories'])) {
                $q->orWhereHas('category', function ($cq) use ($preferences) {
                    $cq->whereIn('slug', $preferences['preferred_categories']);
                });
            }
            if (!empty($preferences['preferred_authors'])) {
                $q->orWhere(function ($aq) use ($preferences) {
                    foreach ($preferences['preferred_authors'] as $author) {
                        $aq->orWhere('author', 'ILIKE', "%{$author}%");
                    }
                });
            }
        });
    }
}
