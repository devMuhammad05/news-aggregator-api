<?php

use App\Jobs\FetchNewsJob;
use App\Services\News\NewsAggregatorService;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');



Schedule::call(function () {
    $newsService = App::make(NewsAggregatorService::class);
    $sources = $newsService->getSources();

    foreach ($sources as $key => $source) {
        dispatch(new FetchNewsJob($key));
    }
})->everyThirtyMinutes()->name('fetch-news')->withoutOverlapping()->onOneServer();
