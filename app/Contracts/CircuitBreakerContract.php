<?php

namespace App\Contracts;

/**
 * Wraps an operation with circuit breaker protection.
 * Prevents repeated calls to a failing service by short-circuiting
 * after a threshold of failures, with automatic recovery.
 */
interface CircuitBreakerContract
{
    /**
     * Execute the callback, falling back if the circuit is open.
     *
     * @param callable $callback The operation to protect
     * @param callable|null $fallback Returned when circuit is open (skips the call)
     */
    public function execute(callable $callback, ?callable $fallback = null): mixed;
}
