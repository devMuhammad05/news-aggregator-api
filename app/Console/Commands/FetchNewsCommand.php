<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use App\Services\News\NewsAggregatorService;
use App\Services\News\Contracts\NewsSourceInterface;

class FetchNewsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fetch:news {--source= : Specific source to fetch from}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch news from configured sources';

    /**
     * Execute the console command.
     */
    public function handle(NewsAggregatorService $newsAggregatorService): int
    {
        $this->info('News aggregation started...');

        $sourceKey = $this->option('source');
        $sources = $newsAggregatorService->getSources();

        if (empty($sources)) {
            $this->error('No news sources configured.');
            return self::FAILURE;
        }

        if ($sourceKey) {
            if (! isset($sources[$sourceKey])) {
                $this->error("Source '{$sourceKey}' not found. Available sources: " . implode(', ', array_keys($sources)));
                return self::FAILURE;
            }

            $this->processSource($newsAggregatorService, $sources[$sourceKey]);
        } else {
            foreach ($sources as $source) {
                $this->processSource($newsAggregatorService, $source);
            }
        }

        $this->info('News aggregation completed!');

        return self::SUCCESS;
    }

    /**
     * Process a single news source.
     */
    private function processSource(NewsAggregatorService $service, NewsSourceInterface $source): void
    {
        $sourceName = $source->getSourceName();

        $this->line("Fetching from: <comment>{$sourceName}</comment>...");

        $result = $service->aggregateFromSource($source);

        if (($result['status'] ?? null) === 'error') {
            $message = $result['error'] ?? 'Unknown error, try again.';

            $this->error("Failed to fetch from {$sourceName}: {$message}");
            return;
        }

        $count = $result['count'] ?? 0;
        $this->info("Successfully fetched {$count} articles from {$sourceName}.");  
    }

}
