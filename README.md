## About Ukhu Blog

Ukhu Blog is a blog module for [Ukhu framework](https://github.com/yanqay/ukhu)

## Prerequisites

- Ukhu framework should be already installed.

## Installation

- Install Ukhu Blog module

    `composer require yanqay/ukhu-blog`

- Run install module command

    `php ukhu module:install blog`

- Add navigation menu to Blog entry page

    `<li><a href="/blog" class="nav-link px-2 link-dark">Blog</a></li>`

- Add new entry in bootstrap/config.php definitions file

    ```
    \App\Blog\Application\Ports\MarkdownInterface::class => function () {
        return new \App\Blog\Infrastructure\Adapters\MarkdownParser(
            __DIR__ . '/../resources/blog'
        );
    },
    ```

- Add Blog routes reference to application routes

    `$router = (require_once __DIR__ . '/../bootstrap/routes/blog.php')($router, $container);`

- Add templates directory to $templateLocations array in bootstrap/config.php template definition

    `'../src/Blog/Infrastructure/Http/Presentation/templates',`

- Inspect git source control changes.

## Contributing

Get in touch if you're interested in contributing to Ukhu.

## Security Vulnerabilities

If you discover a security vulnerability within Ukhu please get in touch.

## Contact

Contact via franckmercado at gmail.com.
