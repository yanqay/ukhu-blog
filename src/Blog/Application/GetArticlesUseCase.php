<?php

declare(strict_types=1);

namespace App\Blog\Application;

use App\Blog\Infrastructure\Adapters\ArticleRepository;

class GetArticlesUseCase
{
    private $articleRepository;
    public function __construct(ArticleRepository $articleRepository)
    {
        $this->articleRepository = $articleRepository;
    }

    public function handle($page)
    {
        $response = array();
        $files = $this->articleRepository->getLatestArticles();

        $perpage = 5;
        $offset = ($page - 1) * $perpage;
        $articles = array_slice($files, $offset, $perpage);

        foreach ($articles as $file) {
            //Log::init('franck', array($file));
            $filename = pathinfo($file->getFilename(), PATHINFO_FILENAME);
            $article = $this->articleRepository->getArticle($filename);
            $response['results'][] = $article;
        }

        $totalPages = ceil(count($files) / $perpage);

        if($page <= $totalPages && $page > 1){
            $response['pagination']['previous_page'] = $page - 1;
        }
        if($page < $totalPages){
            $response['pagination']['next_page'] = $page + 1;
        }

        return $response;
    }
}
