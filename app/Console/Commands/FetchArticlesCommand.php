<?php

namespace App\Console\Commands;

use App\Services\NewsAggregatorService;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

/**
 * Scheduled artisan command that fetches articles from all registered
 * news providers and stores them in the database.
 *
 * Runs hourly via the scheduler container (see docker-compose.yml).
 * Can also be triggered manually: php artisan articles:fetch
 */
#[Signature('articles:fetch')]
#[Description('Fetch latest articles from all news providers and store them in the database')]
class FetchArticlesCommand extends Command
{
    public function handle(NewsAggregatorService $newsAggregatorService)
    {
        try {
            $this->info("Fetching articles from news providers...");
            $newsAggregatorService->store();
            $this->info("Articles fetched and stored successfully.");
        } catch (\Throwable $th) {
            $this->error('Error: ' . $th->getMessage());
            return 1;
        }
    }
}
