<?php

declare(strict_types=1);

use App\DTO\ArticleDto;
use App\Services\News\Contracts\NewsSourceInterface;
use App\Services\News\NewsAggregatorService;

/**
 * Create a mock news source
 */
function createMockSource(string $key, string $name): NewsSourceInterface
{
    $mock = Mockery::mock(NewsSourceInterface::class);
    $mock->shouldReceive('getSourceKey')->andReturn($key);
    $mock->shouldReceive('getSourceName')->andReturn($name);

    return $mock;
}

/**
 * Create an array of ArticleDto objects
 *
 * @return array<ArticleDto>
 */
function createArticleDtos(int $count, string $prefix = 'article'): array
{
    $dtos = [];

    for ($i = 1; $i <= $count; $i++) {
        $dtos[] = new ArticleDto(
            title: "Test Article {$prefix}-{$i}",
            authorName: "Author {$i}",
            description: "Description for article {$i}",
            content: "Content for article {$i}",
            source: 'Test Source',
            sourceUrl: "https://example.com/{$prefix}-{$i}",
            imageUrl: "https://example.com/image-{$i}.jpg",
            publishedAt: now()->subDays($i)
        );
    }

    return $dtos;
}

beforeEach(function () {
    $this->service = new NewsAggregatorService;
});

it('can add a source', function () {
    $mockSource = createMockSource('test_key', 'Test Source');

    $result = $this->service->addSource($mockSource);

    expect($result)->toBeInstanceOf(NewsAggregatorService::class)
        ->and($this->service->getSources())->toHaveCount(1)
        ->and($this->service->getSources())->toHaveKey('test_key');
});

it('can add multiple sources', function () {
    $source1 = createMockSource('source_1', 'Source 1');
    $source2 = createMockSource('source_2', 'Source 2');

    $this->service
        ->addSource($source1)
        ->addSource($source2);

    $sources = $this->service->getSources();

    expect($sources)->toHaveCount(2)
        ->toHaveKeys(['source_1', 'source_2']);
});
