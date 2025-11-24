<?php

declare(strict_types=1);

namespace App\Services\News\Sources;

use App\DTO\ArticleDTO;
use Illuminate\Support\Facades\Date;

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

    /**
     * @param  array<string, mixed>  $params
     * @return array<int, ArticleDTO>
     */
    public function fetchArticles(array $params = []): array
    {
        $defaultParams = [
            'country' => 'us',
            'category' => 'general',
        ];

        $params = array_merge($defaultParams, $params);

        $response = $this->makeRequest($params);

        if (! $response['success']) {
            return [];
        }

        $articles = $response['data']['articles'] ?? [];

        return array_map(function ($article) {
            return new ArticleDTO(
                title: $article['title'] ?? 'Untitled Article',
                author: $article['author'] ?? null,
                description: $article['description'] ?? null,
                content: $article['content'] ?? null,
                source: $article['source']['name'] ?? 'NewsAPI',
                sourceUrl: $article['url'] ?? null,
                imageUrl: $article['urlToImage'] ?? null,
                publishedAt: isset($article['publishedAt']) ? Date::parse($article['publishedAt']) : null,
            );
        }, $articles);
    }
}
