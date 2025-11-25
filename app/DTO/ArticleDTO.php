<?php

declare(strict_types=1);

namespace App\DTO;

use Carbon\Carbon;

class ArticleDTO
{
    public function __construct(
        public readonly string $title,
        public readonly ?string $authorName,
        public readonly ?string $description,
        public readonly ?string $content,
        public readonly string $source,
        public readonly ?string $sourceUrl,
        public readonly ?string $imageUrl,
        public readonly ?Carbon $publishedAt,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'title' => $this->title,
            'author_name' => $this->authorName,
            'description' => $this->description,
            'content' => $this->content ?? '',
            'source' => $this->source,
            'source_url' => $this->sourceUrl,
            'image_url' => $this->imageUrl,
            'published_at' => $this->publishedAt,
        ];
    }
}
