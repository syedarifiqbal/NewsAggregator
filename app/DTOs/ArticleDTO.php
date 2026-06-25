<?php 

namespace App\DTOs;

class ArticleDTO
{
    public function __construct(
        public readonly string $title,
        public readonly ?string $description,
        public readonly string $url,
        public readonly ?string $image,
        public readonly string $source,
        public readonly string $publishedAt,
        public readonly ?string $category = null,
        public readonly ?string $author = null
    ) { }
}