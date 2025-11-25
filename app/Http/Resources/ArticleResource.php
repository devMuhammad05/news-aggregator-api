<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

use App\Models\Article;

/**
 * @mixin Article
 */
class ArticleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'author_name' => $this->author_name,
            'description' => $this->description,
            'content' => $this->content,
            'source' => $this->source,
            'source_url' => $this->source_url,
            'image_url' => $this->image_url,
            'published_at' => $this->published_at->toIso8601String(),
        ];
    }
}
