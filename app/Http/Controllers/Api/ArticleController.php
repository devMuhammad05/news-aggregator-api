<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Actions\GetArticlesAction;
use App\Http\Controllers\Controller;
use App\Http\Resources\ArticleResource;
use Illuminate\Http\Resources\Json\ResourceCollection;

final class ArticleController extends Controller
{
    public function index(GetArticlesAction $action): ResourceCollection
    {
        $articles = $action->execute();

        return ArticleResource::collection($articles);
    }
}
