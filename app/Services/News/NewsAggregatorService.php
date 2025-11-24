<?php

declare(strict_types=1);

namespace App\Services\News;

use Exception;
use App\DTO\ArticleDTO;
use App\Models\Article;
use App\Services\News\Contracts\NewsSourceInterface;
use Illuminate\Support\Facades\Log;

class NewsAggregatorService
{
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
     */
    public function getSources(): array
    {
        return $this->sources;
    }

    /**
     * Fetch and store articles from all sources
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
     */
    public function aggregateFromSource(NewsSourceInterface $source, array $params = [])
    {
        try {
            Log::info('Fetching articles from ' . $source->getSourceName());

            $articles = $source->fetchArticles($params);

            $articleCount = 0;
            foreach ($articles as $articleDto) {
                if (! ($articleDto instanceof ArticleDTO)) {
                    continue;
                }

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
