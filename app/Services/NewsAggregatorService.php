<?php

namespace App\Services;

use App\Services\News\NewsApiProvider;

class NewsAggregatorService
{
    function fetchAll(string $keyword, int $page = 1): array
    {
        $nap = new NewsApiProvider;

        return $nap->fetch($keyword, $page);
    }
}
