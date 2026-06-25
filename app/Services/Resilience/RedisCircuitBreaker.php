<?php

namespace App\Services\Resilience;

use App\Contracts\CircuitBreakerInterface;
use App\Exceptions\CircuitBreakerOpenException;
use Illuminate\Support\Facades\Cache;
use Throwable;

class RedisCircuitBreaker implements CircuitBreakerInterface
{
    public function __construct(
        private readonly string $service,
        private readonly int $failureThreshold = 3,
        private readonly int $recoveryTimeout = 1800 // half hours
    ) {}

    public function execute(
        callable $callback,
        ?callable $fallback = null
    ): mixed {
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

            $this->reset();

            return $result;
        } catch (Throwable $e) {

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

        if ($count === 1) {
            Cache::put(
                $this->failureKey(),
                1,
                now()->addSeconds($this->recoveryTimeout)
            );
        }

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
