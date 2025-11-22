<?php

declare(strict_types=1);

namespace App\Services\News\Sources;

use App\Services\News\Contracts\NewsSourceInterface;

abstract class AbstractNewsSource implements NewsSourceInterface
{
    protected function makeRequest(string $endpoint, array $params = []): array
    {
        return [];
    }
}
