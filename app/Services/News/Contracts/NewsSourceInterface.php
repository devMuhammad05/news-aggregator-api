<?php

declare(strict_types=1);

namespace App\Services\News\Contracts;

interface NewsSourceInterface
{
    /**
     * Get the source name
     */
    public function getSourceName(): string;

    /**
     * Get the source identifier
     */
    public function getSourceKey(): string;

    /**
     * Fetch articles from the news source
     *
     * @param array $params
     */
    public function fetchArticles(array $params = []): array;
}
