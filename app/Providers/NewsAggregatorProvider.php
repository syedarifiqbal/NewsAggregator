<?php

namespace App\Providers;

use App\Contracts\NewsProviderInterface;
use App\Services\News\NewsApiProvider;
use App\Services\NewsAggregatorService;
use Illuminate\Support\ServiceProvider;

class NewsAggregatorProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->tag([
            NewsApiProvider::class,
        ], NewsProviderInterface::class);

        $this->app->singleton(NewsAggregatorService::class, function ($app) {
            return $app->tagged(NewsProviderInterface::class);
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
