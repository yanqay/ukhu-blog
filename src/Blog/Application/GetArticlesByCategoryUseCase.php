<?php

declare(strict_types=1);

namespace App\Blog\Application;

use App\Blog\Infrastructure\Adapters\ArticleRepository;
use App\Blog\Application\PaginateResultsUseCase;

class GetArticlesByCategoryUseCase
{
    private $articleRepository;

    private $paginateResults;
    public function __construct(ArticleRepository $articleRepository, PaginateResultsUseCase $paginateResults)
    {
        $this->articleRepository = $articleRepository;
        $this->paginateResults = $paginateResults;
    }

    public function handle($category, $page)
    {
        $results = $this->articleRepository->getArticlesByCategory($category);

        return $this->paginateResults->handle($results, $page);
    }
}
