<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Article;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Article>
 */
class ArticleFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(),
            'author_name' => fake()->name(),
            'description' => fake()->paragraph(),
            'content' => fake()->paragraphs(3, true),
            'source' => fake()->randomElement(['NewsAPI', 'The Guardian', 'New York Times']),
            'source_url' => fake()->url(),
            'image_url' => fake()->imageUrl(),
            'published_at' => fake()->dateTimeBetween('-30 days', 'now'),
        ];
    }
}
