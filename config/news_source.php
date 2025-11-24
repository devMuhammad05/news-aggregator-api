<?php

use App\Services\News\Sources\NewsApiSource;

return [
    'sources' => [
        'newsapi' => [
            'class' => NewsApiSource::class,
            'enabled' => true,
        ],
        // 'guardian' => [
        //     'class' => \App\Services\News\Sources\GuardianSource::class,
        //     'enabled' => env('NEWS_GUARDIAN_ENABLED', false),
        // ],
    ],
];
