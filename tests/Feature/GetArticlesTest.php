<?php

declare(strict_types=1);

use App\Models\Article;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('it returns paginated articles', function () {
    Article::factory()->count(20)->create();

    $response = $this->getJson('/api/articles');

    $response->assertStatus(200)
        ->assertJsonCount(15, 'data')
        ->assertJsonPath('meta.total', 20)
        ->assertJsonPath('meta.last_page', 2);
});

test('it filters by title', function () {
    Article::factory()->create(['title' => 'Laravel News']);
    Article::factory()->create(['title' => 'PHP Updates']);
    Article::factory()->create(['title' => 'Laravel Tips']);

    $response = $this->getJson('/api/articles?filter[title]=Laravel');

    $response->assertStatus(200)
        ->assertJsonCount(2, 'data');
});

test('it filters by source', function () {
    Article::factory()->create(['source' => 'NewsAPI']);
    Article::factory()->create(['source' => 'The Guardian']);
    Article::factory()->create(['source' => 'NewsAPI']);

    $response = $this->getJson('/api/articles?filter[source]=NewsAPI');

    $response->assertStatus(200)
        ->assertJsonCount(2, 'data');
});

test('it performs full text search', function () {
    Article::factory()->create([
        'title' => 'Climate Change Report',
        'description' => 'Latest findings',
    ]);
    Article::factory()->create([
        'title' => 'Tech News',
        'description' => 'Climate tech innovations',
    ]);
    Article::factory()->create([
        'title' => 'Sports Update',
        'description' => 'Football results',
    ]);

    $response = $this->getJson('/api/articles?filter[search]=climate');

    $response->assertStatus(200)
        ->assertJsonCount(2, 'data');
});

test('it filters by date range', function () {
    Article::factory()->create(['published_at' => '2025-01-01 10:00:00']);
    Article::factory()->create(['published_at' => '2025-01-15 10:00:00']);
    Article::factory()->create(['published_at' => '2025-02-01 10:00:00']);

    $response = $this->getJson('/api/articles?filter[date_from]=2025-01-10&filter[date_to]=2025-01-20');

    $response->assertStatus(200)
        ->assertJsonCount(1, 'data');
});

test('it sorts articles', function () {
    Article::factory()->create(['title' => 'Zebra', 'published_at' => '2025-01-01']);
    Article::factory()->create(['title' => 'Apple', 'published_at' => '2025-01-02']);
    Article::factory()->create(['title' => 'Banana', 'published_at' => '2025-01-03']);

    $response = $this->getJson('/api/articles?sort=title');

    $response->assertStatus(200);

    $data = $response->json('data');
    expect($data[0]['title'])->toBe('Apple');
    expect($data[1]['title'])->toBe('Banana');
    expect($data[2]['title'])->toBe('Zebra');
});

test('it prioritizes custom per page', function () {
    Article::factory()->count(30)->create();

    $response = $this->getJson('/api/articles?per_page=25');

    $response->assertStatus(200)
        ->assertJsonCount(25, 'data')
        ->assertJsonPath('meta.per_page', 25);
});
