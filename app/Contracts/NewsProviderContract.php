<?php

namespace App\Contracts;

interface NewsProviderContract
{
    public function name(): string;

    public function fetch(string $keyword, int $page = 1): array;
}
