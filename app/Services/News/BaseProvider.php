<?php

namespace App\Services\News;

use App\Contracts\NewsProviderContract;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;

/**
 * Base class for all news providers.
 * Provides a pre-configured HTTP client with shared timeout
 * and content-type settings to avoid repetition across providers.
 */
abstract class BaseProvider implements NewsProviderContract
{
    protected function client(): PendingRequest
    {
        return Http::connectTimeout(3)
            ->timeout(10)
            ->acceptJson();
    }
}
