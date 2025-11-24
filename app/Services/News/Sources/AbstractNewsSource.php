<?php

declare(strict_types=1);

namespace App\Services\News\Sources;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use App\Services\News\Contracts\NewsSourceInterface;

abstract class AbstractNewsSource implements NewsSourceInterface
{

    protected string $baseUrl;
    protected string $apiKey;
    protected int $timeout = 30;

    public function __construct()
    {
        $this->apiKey = config("services.news.{$this->getSourceKey()}.api_key");
        $this->baseUrl = config("services.news.{$this->getSourceKey()}.base_url");
    }

    protected function makeRequest(array $params = []): array
    {
        try {
            $response = Http::timeout($this->timeout)
                ->withHeaders([
                    'Authorization' => (string) 'Bearer ' . $this->apiKey,
                    'Accept' => 'application/json',
                ])
                ->get($this->baseUrl, $params);

            if ($response->failed()) {
                Log::error("API request failed", [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);

                return [
                    'success' => false,
                    'error' => 'API request failed with status: ' . $response->status(),
                    'data' => []
                ];
            }

            $data = $response->json();

            return [
                'success' => true,
                'data' => $data
            ];
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            Log::error("API connection error", [
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'error' => 'Connection timeout or network error',
                'data' => []
            ];
        } catch (\Exception $e) {
            Log::error("API request exception", [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
                'data' => []
            ];
        }
    }
}
