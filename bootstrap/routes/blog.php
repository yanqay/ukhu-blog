<?php

use DI\Container;
use League\Route\Router;

return function (Router $router, Container $container): Router {
    $router->group('/blog', function ($router) {
        $router->get('/', \App\Blog\Infrastructure\Http\BlogController::class . '::index');
        $router->get('/article/{article}', \App\Blog\Infrastructure\Http\BlogController::class . '::article');
        $router->get('/category/{category}', \App\Blog\Infrastructure\Http\BlogController::class . '::category');
        $router->get('/tag/{tag}', \App\Blog\Infrastructure\Http\BlogController::class . '::tag');
    })
        ->middleware(new \App\Ukhu\Infrastructure\Middlewares\TemplateMiddleware($container))
        ->middleware(new \App\Ukhu\Infrastructure\Middlewares\WebMiddleware());

    return $router;
};
