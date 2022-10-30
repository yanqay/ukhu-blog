<?php

namespace App\Blog\Infrastructure\Adapters;

use App\Blog\Application\Ports\MarkdownInterface;
use App\Blog\Domain\Entities\Article;
use App\Ukhu\Domain\Exceptions\InternalError;
use App\Ukhu\Infrastructure\Adapters\DTOMapper;
use RegexIterator;

class ArticleRepository
{
    private $markdownParser;
    private $extension = '.md';
    private $separator = '---';
    private $needles = array(
        'title',
        'tags',
        'category',
        'status',
        'author',
        'publish_date',
        'description',
    );
    private $DTOMapper;
    private $markdownDirectory;
    public function __construct(MarkdownInterface $markdown, DTOMapper $DTOMapper)
    {
        $this->markdownParser = $markdown->getParser();
        $this->DTOMapper = $DTOMapper;
        $this->markdownDirectory = $markdown->getMarkdownDirectory();
    }

    /**
     * get article
     *
     * @param string $filename
     * @return App\Blog\Domain\Entities\Article
     */
    public function getArticle(string $filename)
    {
        $filePath = $this->markdownDirectory . '/' . $filename . $this->extension;

        if (!file_exists($filePath)) {
            throw new InternalError("not found given filename");
        }

        $contents = file_get_contents($filePath);
        if (empty($contents)) {
            throw new InternalError("given filename has no contents");
        }

        // parse/split meta & markdown section
        $startPos = 0;
        while (($foundPos = strpos($contents, $this->separator, $startPos)) !== false) {
            $positions[] = $foundPos;
            $startPos = $foundPos + strlen($this->separator);
        }

        if (!isset($positions[0], $positions[1])) {
            throw new InternalError("given filename does not have metadata separator");
        }

        $metaSection = substr($contents, $positions[0], $positions[1] + 3);
        $markdownSection = trim(substr($contents, $positions[1] + 3, strlen($contents)));

        $results = $this->readMetaAttributes($metaSection);
        $results['filename'] = $filename;
        $results['content'] = $this->markdownParser->text($markdownSection);

        return $this->DTOMapper->mapArrayToClass($results, Article::class);
    }

    function stringSeparatorToCamelCase($string, $separator = '_', $capitalizeFirstCharacter = false)
    {
        $str = str_replace($separator, '', ucwords($string, $separator));
        if (!$capitalizeFirstCharacter) {
            $str = lcfirst($str);
        }
        return $str;
    }

    /**
     * get meta attributes to array
     *
     * @param string $metaSection
     * @return array $attributes
     */
    private function readMetaAttributes(string $metaSection): array
    {
        $attributes = array();
        $needles = $this->needles;
        $lines = $this->getsLinesFromMultilineString($metaSection);
        foreach ($lines as $line) {
            foreach ($needles as $key => $needle) {
                $keyToFind = $needle . ': ';
                $position = strpos($line, $keyToFind);
                $newNeedle = $this->stringSeparatorToCamelCase($needle, "_");
                if ($position !== false && $position === 0) {
                    $attributes[$newNeedle] = substr($line, strlen($keyToFind), strlen($line));

                    // unset current needle if position found
                    unset($needles[$key]);
                    continue;
                } else {
                    $attributes[$newNeedle] = '';
                }
            }
        }

        return $attributes;
    }

    /**
     * returns array of individual lines from given multiline string
     *
     * @param string $multilineString
     * @return array $Lines
     */
    private function getsLinesFromMultilineString(string $multilineString): array
    {
        $lines = array();
        if ($handle = fopen('data://text/plain,' . $multilineString, 'r')) {
            while (($line = fgets($handle)) !== false) {
                $lines[] = trim($line);
            }
            fclose($handle);
        }
        return $lines;
    }

    public function getMarkdownFiles()
    {
        $results = array();

        // REGEX: markdown filenames fullpath with dash separated words
        // ex: /path/to/folder/3_532-fsa_fsa-21_21521.md
        // https://regex101.com/r/HYJcr8/1
        $regex = "/^(\/.*\/)(?:\w+(?:-?\w+)+)\.md$/";
        $regexIterator = new RegexIterator(
            new \GlobIterator($this->markdownDirectory . '/*.md'), $regex
        );
        foreach ($regexIterator as $item) {
            $results[] = $item;
        }

        return $results;
    }

    public function getLatestArticles()
    {
        $results = $this->getMarkdownFiles();

        /* TODO
        // order by publish_date desc
        usort($results, function ($a, $b) {
            $first = strtotime($a['attributes']['publish_date']);
            $second = strtotime($b['attributes']['publish_date']);
            if ($first === $second) {
                return 0;
            }
            return ($first < $second) ? 1 : -1;
        });

        // format response data
        foreach ($results as $key => $result) {
            $formattedDate = \Datetime::createFromFormat('Y-m-d h:i:s', $result['attributes']['publish_date']);
            $results[$key]['attributes']['publish_date'] = date_format($formattedDate, 'Y-m-d');
        } */

        return $results;
    }

    public function getArticlesByCategory($category)
    {
        $results = array();
        $files = $this->getMarkdownFiles();

        foreach ($files as $file) {
            $filename = pathinfo($file->getPathname(), PATHINFO_FILENAME);

            $article = $this->getArticle($filename);
            if ($article->getCategory() === $category) {
                $results[] = $article;
            }
        }

        return $results;
    }

    public function getArticlesByTag($tag)
    {
        $results = array();
        $files = $this->getMarkdownFiles();

        foreach ($files as $file) {
            $filename = pathinfo($file->getFilename(), PATHINFO_FILENAME);

            $article = $this->getArticle($filename);
            if (in_array($tag, $article->getTagsInArray())) {
                $results[] = $article;
            }
        }

        return $results;
    }
}
