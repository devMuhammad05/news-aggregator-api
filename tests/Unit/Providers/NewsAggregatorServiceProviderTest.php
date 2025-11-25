<?php

declare(strict_types=1);

use App\Providers\NewsAggregatorServiceProvider;
use App\Services\News\Contracts\NewsSourceInterface;
use App\Services\News\NewsAggregatorService;
use Tests\TestCase;

uses(TestCase::class);

class TestNewsSource implements NewsSourceInterface
{
    public function getSourceName(): string
    {
        return 'Test Source';
    }

    public function getSourceKey(): string
    {
        return 'test_source';
    }

    public function fetchArticles(array $params = []): array
    {
        return [];
    }
}

test('it registers news aggregator service with configured sources', function () {
    $this->app['config']->set('news_source.sources', [
        [
            'class' => TestNewsSource::class,
            'enabled' => true,
        ],
        [
            'class' => 'NonExistentClass',
            'enabled' => true,
        ],
        [
            'class' => TestNewsSource::class,
            'enabled' => false,
        ],
    ]);

    $this->app->register(NewsAggregatorServiceProvider::class);

    $service = $this->app->make(NewsAggregatorService::class);

    expect($service)->toBeInstanceOf(NewsAggregatorService::class);

    $sources = $service->getSources();

    expect($sources)->toHaveCount(1)
        ->and($sources)->toHaveKey('test_source')
        ->and($sources['test_source'])->toBeInstanceOf(TestNewsSource::class);
});
