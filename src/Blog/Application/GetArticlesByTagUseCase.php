<?php

declare(strict_types=1);

namespace App\Blog\Application;

use App\Blog\Infrastructure\Adapters\ArticleRepository;
use App\Blog\Application\PaginateResultsUseCase;

class GetArticlesByTagUseCase
{
    private $articleRepository;

    private $paginateResults;
    public function __construct(ArticleRepository $articleRepository, PaginateResultsUseCase $paginateResults)
    {
        $this->articleRepository = $articleRepository;
        $this->paginateResults = $paginateResults;
    }

    public function handle($tag, $page)
    {
        $results = $this->articleRepository->getArticlesByTag($tag);

        return $this->paginateResults->handle($results, $page);
    }
}
