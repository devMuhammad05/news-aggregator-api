<?php

declare(strict_types=1);

namespace App\Action;

use App\Models\Article;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedSort;
use Spatie\QueryBuilder\QueryBuilder;

class GetArticlesAction
{
    /**
     * Execute the action to get filtered and sorted articles
     *
     * @return LengthAwarePaginator<int, Article>
     */
    public function execute(Request $request): LengthAwarePaginator
    {
        return QueryBuilder::for(Article::class)
            ->allowedFilters($this->getAllowedFilters())
            ->allowedSorts($this->getAllowedSorts())
            ->defaultSort('-published_at')
            ->paginate($request->input('per_page', 15))
            ->appends($request->query());
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
