<?php
namespace App\Parser;


use App\Entity\ParsedNews;
use DOMDocument;
use DOMXPath;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;


class RbcParser extends AbstractParser
{
    protected function parseNews($news_elements): void
    {
        $news_elements->each(/**
         * @throws TransportExceptionInterface
         * @throws ServerExceptionInterface
         * @throws RedirectionExceptionInterface
         * @throws ClientExceptionInterface
         */ function (Crawler $node, $i) use (&$news) {

            $one_news_html = $node->html();

            $doc = new DOMDocument();

            libxml_use_internal_errors(true);

            @$doc->loadHTML(mb_convert_encoding($one_news_html, 'HTML-ENTITIES', 'UTF-8'));

            $xpath = new DOMXPath($doc);

            $link = $xpath->evaluate('string(//a[@class="item__link rm-cm-item-link js-rm-central-column-item-link "]/@href)');

            $title = $xpath->evaluate('string(//span[@class="item__title rm-cm-item-text js-rm-central-column-item-text"]/span)');

            $imageUrl = $xpath->evaluate('string(//picture[@class="item__image smart-image"]/source[last()]/@srcset)') ?? null;

            $value = $this->getNewsValue($link);

            $parsed_news_obj = new ParsedNews();

            $parsed_news_obj
                ->setUrl(mb_convert_encoding($link, 'UTF-8'))
                ->setName(mb_convert_encoding($title, 'UTF-8'))
                ->setSourceUrl(mb_convert_encoding(substr($imageUrl, 0, strpos($imageUrl, ' ') ?: strlen($imageUrl)), 'UTF-8'))
                ->setValue(mb_convert_encoding($value, 'UTF-8'))
                ->setSourceSite(mb_convert_encoding($this->site, 'UTF-8'));

            $this->em->persist($parsed_news_obj);
            $this->em->flush();
        });
    }

    /**
     * @throws RedirectionExceptionInterface
     * @throws ClientExceptionInterface
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     */
    private function getNewsValue(string $news_url):string
    {
        $client = HttpClient::create();
        $response = $client->request('GET', $news_url);

        $statusCode = $response->getStatusCode();
        $contentType = $response->getHeaders()['content-type'][0];

        if ($statusCode !== 200) return '';

        $crawler = new Crawler($response->getContent());

        $value = '';
        $crawler->filter('.article__text.article__text_free p')->each(function (Crawler $node) use (&$value) {
            $value .= $node->text();
        });

        return substr($value, 0, 200);
    }


}
