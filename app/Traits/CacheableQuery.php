<?php

declare(strict_types=1);

namespace App\Traits;

use Illuminate\Support\Facades\Cache;

trait CacheableQuery
{
    /**
     * Cache the results of a query based on the current URL and parameters.
     *
     * @param callable $callback
     * @param array<string, mixed>|null $params
     * @param string $keySuffix
     * @param int $minutes
     * @return mixed
     */
    public function rememberQuery(callable $callback, ?array $params = null, string $keySuffix = '', int $minutes = 10): mixed
    {
        $url = request()->url();
        $queryParams = $params ?? request()->query();

        ksort($queryParams);

        $queryString = http_build_query($queryParams);

        $fullUrl = $queryString ? "{$url}?{$queryString}" : $url;

        $rememberKey = sha1($fullUrl . ':' . $keySuffix);

        return Cache::remember($rememberKey, now()->addMinutes($minutes), $callback);
    }
}
