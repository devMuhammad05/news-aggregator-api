<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Action\GetArticlesAction;
use App\Models\Article;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Tests\TestCase;

class GetArticlesActionTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_returns_paginated_articles(): void
    {
        Article::factory()->count(20)->create();

        $request = new Request;
        $action = new GetArticlesAction;

        $result = $action->execute($request);

        $this->assertCount(15, $result->items());
        $this->assertEquals(20, $result->total());
        $this->assertEquals(2, $result->lastPage());
    }

    // public function test_it_filters_by_title(): void
    // {
    //     Article::factory()->create(['title' => 'Laravel News']);
    //     Article::factory()->create(['title' => 'PHP Updates']);
    //     Article::factory()->create(['title' => 'Laravel Tips']);

    //     $request = new Request(['filter' => ['title' => 'Laravel']]);
    //     $action = new GetArticlesAction;

    //     $result = $action->execute($request);

    //     $this->assertCount(2, $result->items());
    // }

    // public function test_it_filters_by_source(): void
    // {
    //     Article::factory()->create(['source' => 'NewsAPI']);
    //     Article::factory()->create(['source' => 'The Guardian']);
    //     Article::factory()->create(['source' => 'NewsAPI']);

    //     $request = new Request(['filter' => ['source' => 'NewsAPI']]);
    //     $action = new GetArticlesAction;

    //     $result = $action->execute($request);

    //     $this->assertCount(2, $result->items());
    // }

    // public function test_it_performs_full_text_search(): void
    // {
    //     Article::factory()->create([
    //         'title' => 'Climate Change Report',
    //         'description' => 'Latest findings',
    //     ]);
    //     Article::factory()->create([
    //         'title' => 'Tech News',
    //         'description' => 'Climate tech innovations',
    //     ]);
    //     Article::factory()->create([
    //         'title' => 'Sports Update',
    //         'description' => 'Football results',
    //     ]);

    //     $request = new Request(['filter' => ['search' => 'climate']]);
    //     $action = new GetArticlesAction;

    //     $result = $action->execute($request);

    //     $this->assertCount(2, $result->items());
    // }

    // public function test_it_filters_by_date_range(): void
    // {
    //     Article::factory()->create(['published_at' => '2025-01-01 10:00:00']);
    //     Article::factory()->create(['published_at' => '2025-01-15 10:00:00']);
    //     Article::factory()->create(['published_at' => '2025-02-01 10:00:00']);

    //     $request = new Request([
    //         'filter' => [
    //             'date_from' => '2025-01-10',
    //             'date_to' => '2025-01-20',
    //         ],
    //     ]);
    //     $action = new GetArticlesAction;

    //     $result = $action->execute($request);

    //     $this->assertCount(1, $result->items());
    // }

    // public function test_it_sorts_articles(): void
    // {
    //     Article::factory()->create(['title' => 'Zebra', 'published_at' => '2025-01-01']);
    //     Article::factory()->create(['title' => 'Apple', 'published_at' => '2025-01-02']);
    //     Article::factory()->create(['title' => 'Banana', 'published_at' => '2025-01-03']);

    //     $request = new Request(['sort' => 'title']);
    //     $action = new GetArticlesAction;

    //     $result = $action->execute($request);

    //     $this->assertEquals('Apple', $result->items()[0]->title);
    //     $this->assertEquals('Banana', $result->items()[1]->title);
    //     $this->assertEquals('Zebra', $result->items()[2]->title);
    // }

    // public function test_it_respects_custom_per_page(): void
    // {
    //     Article::factory()->count(30)->create();

    //     $request = new Request(['per_page' => 25]);
    //     $action = new GetArticlesAction;

    //     $result = $action->execute($request);

    //     $this->assertCount(25, $result->items());
    //     $this->assertEquals(25, $result->perPage());
    // }
}
