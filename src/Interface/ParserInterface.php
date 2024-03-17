<?php
namespace App\Interface;

use Symfony\Component\DomCrawler\Crawler;

interface ParserInterface
{
    public function parse($news_html, $news_count);
}
