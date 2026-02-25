<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\Article;
use App\Traits\CacheableQuery;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedSort;
use Spatie\QueryBuilder\QueryBuilder;

final class GetArticlesAction
{
    use CacheableQuery;

    /**
     * Execute the action to get filtered and sorted articles
     *
     * @return LengthAwarePaginator<int, Article>
     */
    public function execute(): LengthAwarePaginator
    {
        return $this->rememberQuery(
            fn() => QueryBuilder::for(Article::class)
                ->allowedFilters($this->getAllowedFilters())
                ->allowedSorts($this->getAllowedSorts())
                ->defaultSort('-published_at')
                ->paginate(request()->integer('per_page', 15))
                ->appends(request()->query()),
            keySuffix: 'articles'
        );
    }

    /**
     * Define allowed filters
     *
     * @return array<int, AllowedFilter>
     */
    private function getAllowedFilters(): array
    {
        return [
            AllowedFilter::partial('title'),
            AllowedFilter::partial('author_name'),
            AllowedFilter::partial('description'),
            AllowedFilter::partial('content'),
            AllowedFilter::exact('source'),
            AllowedFilter::scope('date_from', 'publishedAfter'),
            AllowedFilter::scope('date_to', 'publishedBefore'),
            AllowedFilter::scope('search', 'fullTextSearch'),
        ];
    }

    /**
     * Define allowed sorts
     *
     * @return array<int, AllowedSort>
     */
    private function getAllowedSorts(): array
    {
        return [
            AllowedSort::field('title'),
            AllowedSort::field('author_name'),
            AllowedSort::field('source'),
            AllowedSort::field('published_at'),
            AllowedSort::field('created_at'),
        ];
    }
}
