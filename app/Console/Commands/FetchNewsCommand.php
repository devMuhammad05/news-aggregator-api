<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\App;
use App\Services\News\NewsAggregatorService;
use App\Services\News\Sources\NewsApiSource;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class FetchNewsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fetch:news {--source= Specific source to fetch from}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch news from different source';

    /**
     * Create a new console command instance.
     */
    // public function __construct()
    // {
    // }

    /**
     * Execute the console command.
     */
    public function handle(NewsAggregatorService $newsAggregatorService)
    {
        $this->info('News aggregation started...');

        $sourceKey = $this->option('source');

        $sources = $newsAggregatorService->getSources();

        Log::info('sources', [
            'sources' => $sources,
        ]);

        if ($sourceKey) {
            if (! isset($sources[$sourceKey])) {
                $this->error(sprintf('Source %s not found', $sourceKey));

                return 1;
            }

            $this->info((string) 'Fetching from: '.$sourceKey);

            $result = $newsAggregatorService->aggregateFromSource($sources[$sourceKey]);
            if (isset($result['error'])) {
                $this->error(sprintf('Error fetching from %s: ', $sourceKey).$result['error']);
            } else {
                $this->info('Fetched '.($result['count'] ?? 0).(' articles from '.$sourceKey));
            }
        } else {
            $results = $newsAggregatorService->aggregateFromAllSources();
            foreach ($results as $key => $res) {
                if (isset($res['error'])) {
                    $this->error(sprintf('Error fetching from %s: ', $key).$res['error']);
                } else {
                    $this->info('Fetched '.($res['count'] ?? 0).(' articles from '.$key));
                }
            }
        }

        $this->info('News aggregation completed!');

        return 0;
    }
}
