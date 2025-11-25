<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\ArticleFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $title
 * @property string|null $author_name
 * @property string|null $description
 * @property string $content
 * @property string $source
 * @property string|null $source_url
 * @property string|null $image_url
 * @property Carbon $published_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */

class Article extends Model
{
    /** @use HasFactory<ArticleFactory> */
    use HasFactory;

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
