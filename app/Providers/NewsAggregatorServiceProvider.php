<?php

namespace App\Providers;

use App\Contracts\NewsProviderContract;
use App\Services\News\GuardianApiProvider;
use App\Services\News\NewsApiProvider;
use App\Services\News\NYTimesApiProvider;
use App\Services\NewsAggregatorService;
use Illuminate\Support\ServiceProvider;

class NewsAggregatorServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->tag([
            NewsApiProvider::class,
            GuardianApiProvider::class,
            NYTimesApiProvider::class,
        ], NewsProviderContract::class);

        $this->app->singleton(NewsAggregatorService::class, function ($app) {
            return new NewsAggregatorService(
                $app->tagged(NewsProviderContract::class),
                $app->make(\App\Contracts\ArticleRepositoryContract::class),
                $app->make(\App\Contracts\CategroyRepositoryContract::class),
            );
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        
    }
}
