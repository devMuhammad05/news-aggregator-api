<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;

class FetchNewsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fetch:news {--source=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch news from different source';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('News aggregation started...');

        $source = $this->option('source');

        $this->info('Fetching from: '.$source);

        match ($source) {
            'bbc' => $this->info('Fetching BBC news...'),
            'news api' => $this->info('Fetching NEWS news...'),
            default => $this->info('All source'),
        };

        return 1;
    }
}
