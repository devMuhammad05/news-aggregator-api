<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Jobs\FetchNewsJob;
use App\Services\News\NewsAggregatorService;
use Illuminate\Console\Command;
use Illuminate\Events\Dispatcher;

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
     * Create a new console command instance.
     */
    public function __construct(private readonly Dispatcher $dispatcher)
    {
        parent::__construct();
    }

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
                $this->error("Source '{$sourceKey}' not found. Available sources: ".implode(', ', array_keys($sources)));

                return self::FAILURE;
            }

            $this->line("Fetching news from: <comment>{$sources[$sourceKey]->getSourceName()}</comment>...");
            $this->dispatcher->dispatch(new FetchNewsJob($sourceKey));
        } else {
            foreach ($sources as $key => $source) {
                $this->line("Fetching news from: <comment>{$source->getSourceName()}</comment>...");
                $this->dispatcher->dispatch(new FetchNewsJob($key));
            }
        }

        $this->info('News aggregation is processing in the background.');

        return self::SUCCESS;
    }
}
