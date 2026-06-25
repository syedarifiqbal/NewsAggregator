<?php

namespace App\Contracts;

interface CircuitBreakerInterface
{
    public function execute(callable $callback, ?callable $fallback = null): mixed;
}
