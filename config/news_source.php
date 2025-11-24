<?php

use App\Services\News\Sources\NewsApiSource;
use App\Services\News\Sources\NewYorkTimesNewsSource;
use App\Services\News\Sources\TheGuardianNewsSource;

return [
    'sources' => [
        'newsapi' => [
            'class' => NewsApiSource::class,
            'api_key' => env('NEWSAPI_KEY'),
            'enabled' => true,
            'base_url' => 'https://newsapi.org/v2/top-headlines',
        ],
        'guardian' => [
            'class' => TheGuardianNewsSource::class,
            'api_key' => env('GUARDIAN_API_KEY'),
            'enabled' => true,
            'base_url' => 'https://content.guardianapis.com/search',
        ],
        'nytimes' => [
            'class' => NewYorkTimesNewsSource::class,
            'api_key' => env('NEW_YORK_TIMES_API_KEY'),
            'enabled' => true,
            'base_url' => 'https://api.nytimes.com/svc/search/v2/articlesearch.json',
        ],
    ],
];
