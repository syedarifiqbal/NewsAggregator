<?php

namespace App\Console\Commands;

use App\Services\NewsAggregatorService;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('articles:fetch')]
#[Description('This Command will fetch the latest articles from the news providers and store them in the database')]
class FetchArticlesCommand extends Command
{
    public function handle(NewsAggregatorService $newsAggregatorService)
    {
        try {
            $this->info("Fetching Articles from News Providers...");
            $newsAggregatorService->fetchAndStore();
            $this->info("Articles fetched and stored successfully.");
        } catch (\Throwable $th) {
            $this->error('Error: ' . $th->getMessage());
            return 1;
        }
    }
}
