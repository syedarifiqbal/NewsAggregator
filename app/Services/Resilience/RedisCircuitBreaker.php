<?php

namespace App\Services\Resilience;

use App\Contracts\CircuitBreakerContract;
use App\Exceptions\CircuitBreakerOpenException;
use Illuminate\Support\Facades\Cache;
use Throwable;

/**
 * Circuit Breaker prevents repeated calls to a failing external service.
 *
 * States:
 *   CLOSED  -> normal operation, requests pass through
 *   OPEN    -> service is down, requests are short-circuited (skipped)
 *
 * Flow:
 *   1. Each failure increments a counter in Redis
 *   2. After {failureThreshold} failures -> circuit opens for {recoveryTimeout} seconds
 *   3. While open -> fallback is returned immediately (no HTTP call made)
 *   4. After recovery timeout -> Redis key expires, circuit closes, retries resume
 *   5. On success -> failure counter resets
 */
class RedisCircuitBreaker implements CircuitBreakerContract
{
    public function __construct(
        private readonly string $service,
        private readonly int $failureThreshold = 3,
        private readonly int $recoveryTimeout = 1800
    ) {}

    public function execute(
        callable $callback,
        ?callable $fallback = null
    ): mixed {
        // If circuit is open, skip the call entirely
        if ($this->isOpen()) {

            if ($fallback) {
                return $fallback();
            }

            throw new CircuitBreakerOpenException(
                "{$this->service} circuit breaker is open."
            );
        }

        try {
            $result = $callback();

            // Success — reset failure count, keep circuit closed
            $this->reset();

            return $result;
        } catch (Throwable $e) {
            // Failure — increment counter, may trip the circuit open
            $this->recordFailure();

            throw $e;
        }
    }

    private function isOpen(): bool
    {
        return Cache::has($this->openKey());
    }

    private function recordFailure(): void
    {
        $count = Cache::increment($this->failureKey());

        // Set TTL on first failure so counter auto-resets
        if ($count === 1) {
            Cache::put(
                $this->failureKey(),
                1,
                now()->addSeconds($this->recoveryTimeout)
            );
        }

        // Threshold reached — open the circuit
        if ($count >= $this->failureThreshold) {
            Cache::put(
                $this->openKey(),
                true,
                now()->addSeconds($this->recoveryTimeout)
            );
        }
    }

    private function reset(): void
    {
        Cache::forget($this->failureKey());
        Cache::forget($this->openKey());
    }

    private function failureKey(): string
    {
        return "cb:{$this->service}:failures";
    }

    private function openKey(): string
    {
        return "cb:{$this->service}:open";
    }
}
