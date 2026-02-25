<?php

declare(strict_types=1);

namespace App\DTOs;

use Carbon\Carbon;

final readonly class ArticleDTO
{
    public function __construct(
        public string $title,
        public ?string $authorName,
        public ?string $description,
        public ?string $content,
        public string $source,
        public ?string $sourceUrl,
        public ?string $imageUrl,
        public ?Carbon $publishedAt,
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
