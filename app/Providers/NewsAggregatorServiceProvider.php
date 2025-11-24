<?php

declare(strict_types=1);

namespace App\Providers;

use App\Services\News\NewsAggregatorService;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Support\ServiceProvider;

class NewsAggregatorServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(function ($app): NewsAggregatorService {
            $service = new NewsAggregatorService;
            $config = $app->make(Repository::class);
            $sources = $config->get('news_source.sources', []);

            foreach ($sources as $sourceConfig) {
                if (($sourceConfig['enabled'] ?? false) && class_exists($sourceConfig['class'])) {
                    $service->addSource($app->make($sourceConfig['class']));
                }
            }

            return $service;
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
