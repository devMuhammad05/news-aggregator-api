<?php

declare(strict_types=1);

namespace App\Services\News\Sources;

class NewsApiSource extends AbstractNewsSource
{
    public function getSourceName(): string
    {
        return 'NewsAPI';
    }

    public function getSourceKey(): string
    {
        return 'newsapi';
    }

    public function fetchArticles(array $params = []): array
    {
        return [];
    }
}
