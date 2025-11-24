<?php

declare(strict_types=1);

namespace App\Services\News\Sources;

use App\DTO\ArticleDTO;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TheGuardianNewsSource extends AbstractNewsSource
{
    public function getSourceName(): string
    {
        return 'The Guardian';
    }

    public function getSourceKey(): string
    {
        return 'guardian';
    }

    public function fetchArticles(array $params = []): array
    {
        $defaultParams = [
            'show-fields' => 'all',
            'page-size' => 20,
        ];

        $params = array_merge($defaultParams, $params);

        if (isset($params['category'])) {
            $params['section'] = $params['category'];
            unset($params['category']);
        }

        $response = $this->makeRequest($params);

        if (! $response['success']) {
            return [];
        }

        $results = $response['data']['response']['results'] ?? [];

        return array_map(function ($article) {
            return new ArticleDTO(
                title: $article['webTitle'] ?? 'Untitled Article',
                author: $article['fields']['byline'] ?? null,
                description: $article['fields']['trailText'] ?? null,
                content: $article['fields']['body'] ?? null,
                source: 'The Guardian',
                sourceUrl: $article['webUrl'] ?? null,
                imageUrl: $article['fields']['thumbnail'] ?? null,
                publishedAt: isset($article['webPublicationDate']) ? Date::parse($article['webPublicationDate']) : null,
            );
        }, $results);
    }

    /**
     * Override makeRequest 
     */
    protected function makeRequest(array $params = []): array
    {
        $params['api-key'] = $this->apiKey;

        try {
            $response = Http::timeout($this->timeout)
                ->get($this->baseUrl, $params);

            if ($response->failed()) {
                Log::error('Guardian API request failed', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);

                return [
                    'success' => false,
                    'error' => 'API request failed with status: ' . $response->status(),
                    'data' => [],
                ];
            }

            return [
                'success' => true,
                'data' => $response->json(),
            ];
        } catch (\Exception $e) {
            Log::error('Guardian API request exception', [
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
                'data' => [],
            ];
        }
    }
}
