<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ArticleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'url' => $this->url,
            'image' => $this->image ?? 'https://placehold.co/600x400',
            'source' => $this->source,
            'provider' => $this->provider,
            'category' => $this->category ? $this->category->name : null,
            'author' => $this->author,
            'published_at' => $this->published_at,
        ];
    }
}
