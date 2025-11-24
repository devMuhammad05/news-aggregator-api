<?php

declare(strict_types=1);

namespace App\Services\News;

use App\Models\Article;
use App\Services\News\Contracts\NewsSourceInterface;
use Exception;
use Illuminate\Support\Facades\Log;

class NewsAggregatorService
{
    /**
     * @var array<string, NewsSourceInterface>
     */
    protected array $sources = [];

    /**
     * Add a news source
     */
    public function addSource(NewsSourceInterface $newsSourceInterface): self
    {
        $this->sources[$newsSourceInterface->getSourceKey()] = $newsSourceInterface;

        return $this;
    }

    /**
     * Get all news sources
     *
     * @return array<string, NewsSourceInterface>
     */
    public function getSources(): array
    {
        return $this->sources;
    }

    /**
     * Fetch and store articles from all sources
     *
     * @param  array<string, mixed>  $params
     * @return array<string, array<string, mixed>>
     */
    public function aggregateFromAllSources(array $params = []): array
    {
        $results = [];

        foreach ($this->sources as $sourceKey => $source) {
            $results[$sourceKey] = $this->aggregateFromSource($source, $params);
        }

        return $results;
    }

    /**
     * Fetch and store articles from a specific source
     *
     * @param  array<string, mixed>  $params
     * @return array<string, mixed>
     */
    public function aggregateFromSource(NewsSourceInterface $source, array $params = [])
    {
        try {
            Log::info('Fetching articles from '.$source->getSourceName());

            $articles = $source->fetchArticles($params);

            $articleCount = 0;
            foreach ($articles as $articleDto) {

                $attributes = $articleDto->toArray();

                $keys = ['source_url' => $articleDto->sourceUrl];
                if (empty($articleDto->sourceUrl)) {
                    $keys = [
                        'title' => $articleDto->title,
                        'source' => $articleDto->source,
                    ];
                }

                Article::updateOrCreate(
                    $keys,
                    $attributes
                );
                $articleCount++;
            }

            return [
                'source' => $source->getSourceName(),
                'count' => $articleCount,
                'status' => 'success',
            ];
        } catch (Exception $exception) {
            Log::error(sprintf('Error aggregating from %s: %s', $source->getSourceName(), $exception->getMessage()));

            return [
                'source' => $source->getSourceName(),
                'error' => $exception->getMessage(),
                'status' => 'error',
            ];
        }
    }
}
