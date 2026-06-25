<?php

namespace App\DTOs;

/**
 * Unified data structure for articles across all news providers.
 * Each provider maps its own API response format into this DTO
 * before the article is persisted to the database.
 */
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
    ) {}
}
