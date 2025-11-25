<?php

declare(strict_types=1);

namespace App\Services\News\Sources;

use App\DTO\ArticleDTO;
use Exception;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class NewYorkTimesNewsSource extends AbstractNewsSource
{
    public function getSourceName(): string
    {

        return 'New York Times';
    }

    public function getSourceKey(): string
    {
        return 'nytimes';
    }

    /**
     * @param  array<string, mixed>  $params
     * @return array<int, ArticleDTO>
     */
    public function fetchArticles(array $params = []): array
    {
        $defaultParams = [
            'sort' => 'newest',
        ];

        $params = array_merge($defaultParams, $params);

        if (isset($params['category'])) {
            $params['fq'] = 'section_name:("'.$params['category'].'")';
            unset($params['category']);
        }

        $response = $this->makeRequest($params);

        if (! $response['success']) {
            return [];
        }

        $docs = $response['data']['response']['docs'] ?? [];

        return array_map(function ($article) {
            return new ArticleDTO(
                title: $article['headline']['main'] ?? 'Untitled Article',
                authorName: $article['byline']['original'] ?? null,
                description: $article['abstract'] ?? $article['snippet'] ?? null,
                content: $article['lead_paragraph'] ?? null,
                source: 'New York Times',
                sourceUrl: $article['web_url'] ?? null,
                imageUrl: $this->getImageUrl($article['multimedia'] ?? []),
                publishedAt: isset($article['pub_date']) ? Date::parse($article['pub_date']) : null,
            );
        }, $docs);
    }

    /**
     * @param  array<int, array<string, mixed>>  $multimedia
     */
    private function getImageUrl(array $multimedia): ?string
    {
        if (empty($multimedia)) {
            return null;
        }

        foreach ($multimedia as $media) {
            if (($media['subtype'] ?? '') === 'xlarge') {
                return 'https://static01.nyt.com/'.$media['url'];
            }
        }

        if (isset($multimedia[0]['url'])) {
            return 'https://static01.nyt.com/'.$multimedia[0]['url'];
        }

        return null;
    }

    /**
     * Override makeRequest
     *
     * @param  array<string, mixed>  $params
     * @return array<string, mixed>
     */
    protected function makeRequest(array $params = []): array
    {
        $params['api-key'] = $this->apiKey;

        try {
            $response = Http::timeout($this->timeout)
                ->get($this->baseUrl, $params);

            if ($response->failed()) {
                Log::error('NYT API request failed', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);

                return [
                    'success' => false,
                    'error' => 'API request failed with status: '.$response->status(),
                    'data' => [],
                ];
            }

            return [
                'success' => true,
                'data' => $response->json(),
            ];
        } catch (Exception $exception) {
            Log::error('NYT API request exception', [
                'error' => $exception->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => $exception->getMessage(),
                'data' => [],
            ];
        }
    }
}
