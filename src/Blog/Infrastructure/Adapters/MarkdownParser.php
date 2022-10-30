<?php

namespace App\Blog\Infrastructure\Adapters;

use App\Blog\Application\Ports\MarkdownInterface;
use Parsedown;

class MarkdownParser implements MarkdownInterface
{
    private $parser;
    private $markdownDirectory;
    function __construct($markdownDirectory)
    {
        $this->parser = new Parsedown();
        $this->markdownDirectory = $markdownDirectory;
    }

    public function getParser()
    {
        return $this->parser;
    }

    public function getMarkdownDirectory()
    {
        return $this->markdownDirectory;
    }
}