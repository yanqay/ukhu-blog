<?php

namespace App\Blog\Application\Ports;

interface MarkdownInterface
{
    public function getParser();
    public function getMarkdownDirectory();
}