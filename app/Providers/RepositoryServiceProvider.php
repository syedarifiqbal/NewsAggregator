<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(
            \App\Contracts\ArticleRepositoryContract::class,
            \App\Repositories\ArticleRepository::class
        );

        $this->app->bind(
            \App\Contracts\UserRepositoryContract::class,
            \App\Repositories\UserRepository::class
        );

        $this->app->bind(
            \App\Contracts\UserPreferenceRepositoryContract::class,
            \App\Repositories\UserPreferenceRepository::class
        );

        $this->app->bind(
            \App\Contracts\CategroyRepositoryContract::class,
            function ($app) {
                return new \App\Repositories\CachedCategoryRepository(
                    $app->make(\App\Repositories\CategoryRepository::class)
                );
            }
        );
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
