<?php

namespace App\Blog\Infrastructure\Http;

use App\Blog\Application\GetArticlesByCategoryUseCase;
use App\Blog\Application\GetArticlesByTagUseCase;
use App\Blog\Application\GetArticlesUseCase;
use App\Ukhu\Application\Ports\TemplateInterface;
use App\Ukhu\Infrastructure\Http\Controller;
use App\Blog\Infrastructure\Adapters\ArticleRepository;
use Laminas\Diactoros\ServerRequest;

class BlogController extends Controller
{
    private $articleRepository;
    private $getArticlesUseCase;
    private $getArticlesByCategoryUserCase;
    private $getArticlesByTagUserCase;
    public function __construct(
        TemplateInterface $template, 
        GetArticlesUseCase $getArticlesUseCase,
        GetArticlesByCategoryUseCase $getArticlesByCategoryUserCase,
        ArticleRepository $articleRepository,
        GetArticlesByTagUseCase $getArticlesByTagUserCase
    ) {
        $this->articleRepository = $articleRepository;
        $this->getArticlesUseCase = $getArticlesUseCase;
        $this->getArticlesByCategoryUserCase = $getArticlesByCategoryUserCase;
        parent::__construct($template);
        $this->getArticlesByTagUserCase = $getArticlesByTagUserCase;
    }

    public function article($request, $params)
    {
        $article = $this->articleRepository->getArticle($params['article']);

        $data = [
            'article' => $article
        ];

        return $this->view('blog/article.html', $data);
    }

    public function index(ServerRequest $request)
    {
        $params = $request->getQueryParams();
        $currentPage = isset($params['page']) && $params['page'] > 0 ? $params['page'] : 1;

        $response = $this->getArticlesUseCase->handle($currentPage);

        $data = [
            'articles' => $response['results'],
            'pagination' => $response['pagination']
        ];

        return $this->view('blog/blog.html', $data);
    }

    public function category(ServerRequest $request)
    {
        $params = $request->getQueryParams();
        $currentPage = isset($params['page']) && $params['page'] > 0 ? $params['page'] : 1;
        $category = $request->getAttribute('category');
        $response = $this->getArticlesByCategoryUserCase->handle($category, $currentPage);

        $data = [
            'results' => $response['results'],
            'category' => $category,
            'pagination' => $response['pagination']
        ];

        return $this->view('blog/category.html', $data);
    }

    public function tag(ServerRequest $request)
    {
        $params = $request->getQueryParams();
        $currentPage = isset($params['page']) && $params['page'] > 0 ? $params['page'] : 1;
        $tag = $request->getAttribute('tag');

        $response = $this->getArticlesByTagUserCase->handle($tag, $currentPage);

        $data = [
            'results' => $response['results'],
            'tag' => $tag,
            'pagination' => $response['pagination']
        ];

        return $this->view('blog/tag.html', $data);
    }
}
