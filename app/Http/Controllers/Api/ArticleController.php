<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Contracts\Routing\ResponseFactory;
use App\Action\GetArticlesAction;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class ArticleController extends Controller
{
    public function __construct(private readonly ResponseFactory $responseFactory)
    {
    }

    /**
     * Display a listing of the resource.
     */
    public function index(GetArticlesAction $action, Request $request): JsonResponse
    {
        $articles = $action->execute($request);

        return $this->responseFactory->json([
            'success' => true,
            'data' => $articles->items(),
            'meta' => [
                'current_page' => $articles->currentPage(),
                'from' => $articles->firstItem(),
                'last_page' => $articles->lastPage(),
                'per_page' => $articles->perPage(),
                'to' => $articles->lastItem(),
                'total' => $articles->total(),
            ],
            'links' => [
                'first' => $articles->url(1),
                'last' => $articles->url($articles->lastPage()),
                'prev' => $articles->previousPageUrl(),
                'next' => $articles->nextPageUrl(),
            ],
        ]);
    }
}
