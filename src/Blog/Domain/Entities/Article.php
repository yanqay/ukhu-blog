<?php

declare(strict_types=1);

namespace App\Blog\Domain\Entities;

use Assert\Assert;
use Carbon\Carbon;

class Article
{
    private $title;
    private $status;
    private $publishDate;
    private $tags;
    private $category;
    private $content;
    private $filename;
    private $description;
    private $publishDateHumanFormat;
    private $author;
    public function __construct($title, $status, $author, $publishDate, $tags, $category, $content, $filename, $description)
    {
        Assert::that($title)->notEmpty('Title can not be empty');
        $this->title = $title;

        Assert::that($status)->notEmpty('Status can not be empty');
        $this->status = $status;

        Assert::that($author)->notEmpty('Author can not be empty');
        $this->author = $author;

        Assert::that($publishDate)->date('Y-m-d H:i:s', 'Not valid Date');
        $this->publishDate = $publishDate;
        $this->publishDateHumanFormat = Carbon::createFromTimeStamp(
            strtotime($this->publishDate)
        )->diffForHumans();

        Assert::that($content)->notEmpty('Content can not be empty');
        $this->content = $content;

        Assert::that($description)->notEmpty('Description can not be empty');
        $this->description = $description;

        $this->tags = $tags;
        $this->category = $category;
        $this->filename = $filename;

    }

    public function getTitle()
    {
        return $this->title;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function getAuthor()
    {
        return $this->author;
    }

    public function getPublishDate()
    {
        return $this->publishDate;
    }

    public function getTags()
    {
        return $this->tags;
    }

    public function getTagsInArray()
    {
        return explode(',', str_replace(' ', '', $this->tags));
    }

    public function getCategory()
    {
        return $this->category;
    }

    public function getContent()
    {
        return $this->content;
    }

    public function getFilename()
    {
        return $this->filename;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function getPublishDateHumanFormat()
    {
        return $this->publishDateHumanFormat;
    }
}
