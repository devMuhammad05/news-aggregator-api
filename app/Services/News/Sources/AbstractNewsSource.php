<?php

declare(strict_types=1);

namespace App\Services\News\Sources;

use Illuminate\Contracts\Config\Repository;
use Illuminate\Http\Client\ConnectionException;
use Exception;
use App\Services\News\Contracts\NewsSourceInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

abstract class AbstractNewsSource implements NewsSourceInterface
{
    protected string $baseUrl;

    protected string $apiKey;

    protected int $timeout = 30;

    public function __construct(Repository $repository)
    {
        $this->apiKey = $repository->get(sprintf('services.news.%s.api_key', $this->getSourceKey()));
        $this->baseUrl = $repository->get(sprintf('services.news.%s.base_url', $this->getSourceKey()));
    }

    protected function makeRequest(array $params = []): array
    {
        try {
            $response = Http::timeout($this->timeout)
                ->withHeaders([
                    'Authorization' => (string) 'Bearer '.$this->apiKey,
                    'Accept' => 'application/json',
                ])
                ->get($this->baseUrl, $params);

            if ($response->failed()) {
                Log::error('API request failed', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);

                return [
                    'success' => false,
                    'error' => 'API request failed with status: '.$response->status(),
                    'data' => [],
                ];
            }

            $data = $response->json();

            return [
                'success' => true,
                'data' => $data,
            ];
        } catch (ConnectionException $e) {
            Log::error('API connection error', [
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => 'Connection timeout or network error',
                'data' => [],
            ];
        } catch (Exception $e) {
            Log::error('API request exception', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
                'data' => [],
            ];
        }
    }
}
