<?php

namespace App\Contracts;

interface NewsProviderInterface
{
    public function name(): string;

    public function fetch(string $keyword, int $page = 1): array;
}
