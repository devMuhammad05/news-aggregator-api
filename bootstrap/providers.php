<?php

declare(strict_types=1);

use App\Providers\AppServiceProvider;
use App\Providers\NewsAggregatorServiceProvider;

return [
    AppServiceProvider::class,
    NewsAggregatorServiceProvider::class,
];
