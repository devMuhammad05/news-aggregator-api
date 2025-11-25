<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Services\News\NewsAggregatorService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class FetchNewsJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 5;

    /**
     * The number of seconds the job can run before timing out.
     *
     * @var int
     */
    public $timeout = 120;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public readonly string $sourceKey
    ) {}

    /**
     * Execute the job.
     */
    public function handle(NewsAggregatorService $newsAggregatorService): void
    {
        $sources = $newsAggregatorService->getSources();

        if (! isset($sources[$this->sourceKey])) {

            return;
        }

        $source = $sources[$this->sourceKey];

        $result = $newsAggregatorService->aggregateFromSource($source);

        if (($result['status'] ?? null) === 'error') {

            return;
        }

    }
}
