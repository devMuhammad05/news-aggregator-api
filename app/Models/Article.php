<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    protected function casts(): array
    {
        return [
            'published_at' => 'datetime',
        ];
    }

    /**
     * Scope to filter articles published after a given date
     *
     * @param  Builder<Article>  $query
     */
    #[Scope]
    protected function publishedAfter(Builder $query, string $date): void
    {
        $query->where('published_at', '>=', $date);
    }

    /**
     * Scope to filter articles published before a given date
     *
     * @param  Builder<Article>  $query
     */
    #[Scope]
    protected function publishedBefore(Builder $query, string $date): void
    {
        $query->where('published_at', '<=', $date);
    }

    /**
     * Scope for full-text search across multiple fields
     *
     * @param  Builder<Article>  $query
     */
    #[Scope]
    protected function fullTextSearch(Builder $query, string $search): void
    {
        $query->where(function (Builder $q) use ($search) {
            $q->where('title', 'like', "%{$search}%")
                ->orWhere('description', 'like', "%{$search}%")
                ->orWhere('content', 'like', "%{$search}%")
                ->orWhere('author_name', 'like', "%{$search}%");
        });
    }
}
