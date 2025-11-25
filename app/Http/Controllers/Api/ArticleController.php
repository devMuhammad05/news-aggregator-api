<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Action\GetArticlesAction;
use App\Http\Controllers\Controller;
use App\Http\Resources\ArticleResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class ArticleController extends Controller
{
    public function index(GetArticlesAction $action, Request $request): ResourceCollection
    {
        $articles = $action->execute($request);

        return ArticleResource::collection($articles);
    }
}
