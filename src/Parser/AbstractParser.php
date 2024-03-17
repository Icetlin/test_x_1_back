<?php

namespace App\Parser;


use App\Interface\ParserInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\DomCrawler\Crawler;

abstract class AbstractParser implements ParserInterface
{
    protected string $xpath_query;
    protected EntityManagerInterface $em;
    protected string $site;

    public function __construct(
        string $xpath_query,
        EntityManagerInterface $em,
        string $site
    )
    {
        $this->xpath_query = $xpath_query;
        $this->em = $em;
        $this->site = $site;
    }

    public function parse($news_html, $news_count)
    {
        $crawler = new Crawler($news_html);

        $news_elements = $crawler->filterXPath($this->xpath_query)->slice(0, $news_count);

        return $this->parseNews($news_elements);
    }

    abstract protected function parseNews($news_elements);
}