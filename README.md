# News Aggregator API

A robust Laravel-based REST API that aggregates news articles from multiple sources including The Guardian, New York Times, and NewsAPI. Built with modern PHP practices, featuring job-based processing, DTOs, and comprehensive filtering capabilities.

## 🚀 Features

-   **Multi-Source Aggregation**: Fetch news from The Guardian, New York Times, and NewsAPI
-   **Job-Based Processing**: Asynchronous article fetching using Laravel queues
-   **Scheduled Updates**: Automatic news fetching every 30 minutes
-   **Advanced Filtering**: Filter by title, author, source, date range, and full-text search
-   **Flexible Sorting**: Sort articles by multiple fields
-   **Pagination Support**: Efficient data retrieval with customizable page sizes
-   **Type-Safe Architecture**: Strict typing with PHPStan level 7 compliance
-   **Comprehensive Testing**: Feature and unit tests with Pest PHP

## Requirements

-   **PHP**: 8.2 or higher
-   **Laravel**: 12.x
-   **Composer**: 2.x
-   **Database**: MySQL 8.0+ / PostgreSQL 13+ / SQLite 3.35+
-   **Queue Driver**: Database, Redis, or other Laravel-supported drivers

## 🛠️ Installation

### 1. Clone the Repository

```bash
git clone https://github.com/devMuhammad05/news-aggregator-api.git
cd news-aggregator-api
```

### 2. Install Dependencies

```bash
composer install
```

### 3. Environment Configuration

```bash
cp .env.example .env
php artisan key:generate
```

### 4. Configure Database

Update your `.env` file with database credentials:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=news_aggregator
DB_USERNAME=root
DB_PASSWORD=
```

### 5. Configure Queue Connection

```env
QUEUE_CONNECTION=database
```

### 6. Run Migrations

```bash
php artisan migrate
```

### 7. Configure News Source API Keys

Add your API keys to the `.env` file:

```env
# NewsAPI (https://newsapi.org)
NEWSAPI_KEY=your_newsapi_key_here

# The Guardian (https://open-platform.theguardian.com)
GUARDIAN_API_KEY=your_guardian_api_key_here

# New York Times (https://developer.nytimes.com)
NEW_YORK_TIMES_API_KEY=your_nyt_api_key_here
```

## 🎯 Quick Start

### Using Composer Scripts

```bash
# Complete setup (install, configure, migrate)
composer setup

# Start development servers (API, queue worker, and Vite)
composer dev

# Run tests
composer test
```

### Manual Commands

```bash
# Start the development server
php artisan serve

# Start the queue worker (in a separate terminal)
php artisan queue:work

# Fetch news manually
php artisan fetch:news

# Fetch from a specific source
php artisan fetch:news --source=guardian
php artisan fetch:news --source=nytimes
php artisan fetch:news --source=newsapi
```

## Architecture

### Overview

The application follows a clean, modular architecture with clear separation of concerns:


### Key Components

#### 1. **Console Command** (`FetchNewsCommand`)

-   Entry point for manual news fetching
-   Accepts `--source` option to fetch from specific sources
-   Dispatches jobs to the queue for asynchronous processing

```bash
php artisan fetch:news --source=guardian
```

#### 2. **Jobs** (`FetchNewsJob`)

-   Processes news fetching for a single source
-   Configured with 5 retry attempts and 120-second timeout
-   Handles errors gracefully without blocking other sources

#### 3. **News Aggregator Service** (`NewsAggregatorService`)

-   Central orchestrator for news aggregation
-   Manages multiple news sources
-   Handles article persistence with duplicate detection
-   Uses `updateOrCreate` to prevent duplicate articles

#### 4. **News Sources**

Each news source extends `AbstractNewsSource` and implements `NewsSourceInterface`:

-   **TheGuardianNewsSource**: Fetches from The Guardian API
-   **NewYorkTimesNewsSource**: Fetches from NYT Article Search API
-   **NewsApiSource**: Fetches from NewsAPI.org

Features:

-   HTTP request handling with timeout
-   Error logging and graceful failure handling
-   Transforms API responses into standardized `ArticleDTO` objects

#### 5. **Data Transfer Object** (`ArticleDTO`)

Standardizes article data across different sources:

```php
ArticleDTO {
    +title: string
    +authorName: ?string
    +description: ?string
    +content: ?string
    +source: string
    +sourceUrl: ?string
    +imageUrl: ?string
    +publishedAt: ?Carbon
}
```

#### 6. **Article Model**

Eloquent model with custom scopes:

-   `publishedAfter(string $date)`: Filter articles after a date
-   `publishedBefore(string $date)`: Filter articles before a date
-   `fullTextSearch(string $search)`: Search across title, description, content, and author

#### 7. **Scheduled Tasks**

Automatic news fetching configured in `routes/console.php`:

-   Runs every 30 minutes
-   Prevents overlapping executions
-   Runs on a single server in multi-server environments

## API Documentation

### Base URL

```
http://localhost:8000/api/v1
```

### Endpoints

#### Get Articles

Retrieve a paginated list of articles with advanced filtering and sorting.

**Endpoint:**

```http
GET /articles
```

**Query Parameters:**

| Parameter             | Type    | Description                                 | Example                          |
| --------------------- | ------- | ------------------------------------------- | -------------------------------- |
| `filter[title]`       | string  | Partial match on article title              | `filter[title]=Laravel`          |
| `filter[author_name]` | string  | Partial match on author name                | `filter[author_name]=John`       |
| `filter[description]` | string  | Partial match on description                | `filter[description]=technology` |
| `filter[content]`     | string  | Partial match on content                    | `filter[content]=PHP`            |
| `filter[source]`      | string  | Exact match on source name                  | `filter[source]=The Guardian`    |
| `filter[date_from]`   | date    | Articles published after this date          | `filter[date_from]=2025-01-01`   |
| `filter[date_to]`     | date    | Articles published before this date         | `filter[date_to]=2025-12-31`     |
| `filter[search]`      | string  | Full-text search across all fields          | `filter[search]=climate change`  |
| `sort`                | string  | Sort field (prefix with `-` for descending) | `sort=-published_at`             |
| `per_page`            | integer | Results per page (default: 15)              | `per_page=20`                    |
| `page`                | integer | Page number                                 | `page=2`                         |

**Available Sort Fields:**

-   `title`
-   `author_name`
-   `source`
-   `published_at` (default: `-published_at`)
-   `created_at`

**Response Format:**

```json
{
    "data": [
        {
            "id": 1,
            "title": "Breaking News: Laravel 12 Released",
            "author_name": "John Doe",
            "description": "Laravel 12 brings exciting new features...",
            "content": "Full article content here...",
            "source": "The Guardian",
            "source_url": "https://example.com/article",
            "image_url": "https://example.com/image.jpg",
            "published_at": "2025-11-25T10:30:00+00:00"
        }
    ],
    "links": {
        "first": "http://localhost:8000/api/v1/articles?page=1",
        "last": "http://localhost:8000/api/v1/articles?page=10",
        "prev": null,
        "next": "http://localhost:8000/api/v1/articles?page=2"
    },
    "meta": {
        "current_page": 1,
        "from": 1,
        "last_page": 10,
        "path": "http://localhost:8000/api/v1/articles",
        "per_page": 15,
        "to": 15,
        "total": 150
    }
}
```

### Example Requests

#### Basic Request

```bash
curl -X GET "http://localhost:8000/api/v1/articles"
```

#### Filter by Source

```bash
curl -X GET "http://localhost:8000/api/v1/articles?filter[source]=The Guardian"
```

#### Search with Date Range

```bash
curl -X GET "http://localhost:8000/api/v1/articles?filter[search]=technology&filter[date_from]=2025-01-01&filter[date_to]=2025-12-31"
```

#### Sort by Published Date (Newest First)

```bash
curl -X GET "http://localhost:8000/api/v1/articles?sort=-published_at&per_page=20"
```

#### Complex Query

```bash
curl -X GET "http://localhost:8000/api/v1/articles?filter[author_name]=Smith&filter[source]=New York Times&sort=-published_at&per_page=10"
```

#### Full-Text Search

```bash
curl -X GET "http://localhost:8000/api/v1/articles?filter[search]=climate change"
```

## 🧪 Testing

The project uses Pest PHP for testing with comprehensive coverage.

### Run All Tests

```bash
php artisan test
# or
composer test
```

### Run Specific Test Suites

```bash
# Feature tests only
php artisan test --testsuite=Feature

# Unit tests only
php artisan test --testsuite=Unit
```

## 🔧 Configuration

### News Sources Configuration

Edit `config/news_source.php` to enable/disable sources or modify settings:

```php
return [
    'sources' => [
        'newsapi' => [
            'class' => NewsApiSource::class,
            'api_key' => env('NEWSAPI_KEY'),
            'enabled' => true,
            'base_url' => 'https://newsapi.org/v2/top-headlines',
        ],
        'guardian' => [
            'class' => TheGuardianNewsSource::class,
            'api_key' => env('GUARDIAN_API_KEY'),
            'enabled' => true,
            'base_url' => 'https://content.guardianapis.com/search',
        ],
        'nytimes' => [
            'class' => NewYorkTimesNewsSource::class,
            'api_key' => env('NEW_YORK_TIMES_API_KEY'),
            'enabled' => true,
            'base_url' => 'https://api.nytimes.com/svc/search/v2/articlesearch.json',
        ],
    ],
];
```

### Scheduled Task Configuration

The scheduler is configured in `routes/console.php`:

```php
Schedule::call(function () {
    $newsService = App::make(NewsAggregatorService::class);
    $sources = $newsService->getSources();

    foreach ($sources as $key => $source) {
        dispatch(new FetchNewsJob($key));
    }
})
->everyThirtyMinutes()
->name('fetch-news')
->withoutOverlapping()
->onOneServer();
```

To run the scheduler, add this to your cron:

```bash
* * * * * cd /path-to-your-project && php artisan schedule:run 
```

## 🔍 Code Quality

### Static Analysis

The project uses PHPStan for static analysis at level 7:

```bash
vendor/bin/phpstan analyse
```

### Code Style

Laravel Pint is configured for code formatting:

```bash
vendor/bin/pint
```

### Refactoring

Rector is configured for automated refactoring:

```bash
vendor/bin/rector process
```

### 4. Test API Endpoints

```bash
curl http://localhost:8000/api/v1/articles
```

## Adding a New News Source

To add a new news source:

1. **Create a new source class** in `app/Services/News/Sources/`:

```php
<?php

namespace App\Services\News\Sources;

use App\DTO\ArticleDTO;

class YourNewsSource extends AbstractNewsSource
{
    public function getSourceName(): string
    {
        return 'Your Source Name';
    }

    public function getSourceKey(): string
    {
        return 'your_source_key';
    }

    public function fetchArticles(array $params = []): array
    {
        $response = $this->makeRequest($params);

        if (!$response['success']) {
            return [];
        }

        // Transform API response to ArticleDTO array
        return array_map(function ($article) {
            return new ArticleDTO(
                title: $article['title'],
                authorName: $article['author'] ?? null,
                description: $article['description'] ?? null,
                content: $article['content'] ?? null,
                source: $this->getSourceName(),
                sourceUrl: $article['url'] ?? null,
                imageUrl: $article['image'] ?? null,
                publishedAt: isset($article['date']) ? Date::parse($article['date']) : null,
            );
        }, $response['data']['articles'] ?? []);
    }
}
```

2. **Register the source** in `config/news_source.php`:

```php
'your_source_key' => [
    'class' => YourNewsSource::class,
    'api_key' => env('YOUR_SOURCE_API_KEY'),
    'enabled' => true,
    'base_url' => 'https://api.yoursource.com/endpoint',
],
```

3. **Add API key** to `.env`:

```env
YOUR_SOURCE_API_KEY=your_api_key_here
```

## Author 

**devMuhammad05**

* GitHub: [@devMuhammad05](https://github.com/devMuhammad05)