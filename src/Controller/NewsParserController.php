<?php

namespace App\Controller;

use AllowDynamicProperties;
use App\Factory\NewsParserFactory;
use Doctrine\ORM\EntityManagerInterface;
use phpDocumentor\Reflection\Types\This;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use function PHPUnit\Framework\isNull;

#[AllowDynamicProperties] class NewsParserController extends AbstractController
{
    private NewsParserFactory $factory;
    public EntityManagerInterface $em;
    public ParameterBagInterface $params;

    public function __construct(
        ParameterBagInterface $params,
        EntityManagerInterface $em
    )
    {
        $this->em = $em;
        $this->factory = new NewsParserFactory($this->em);
        $this->sites = $params->get('sites');
        $this->params = $params;
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ClientExceptionInterface
     * @throws \Exception
     */
    #[Route('/news/parser/{site}/{news_count}', name: 'app_news_parser', methods: ['GET', 'POST'])]
    public function index(
        Request $request,
        string $site,
        string $news_count
    ): Response
    {

        $news_url    = $this->sites[$site] ?? null;

        if (is_null($news_url)) return new Response('There are no code to parse this site at this time', 500);

        $news_parser = $this->factory->createParser($site);

        $http_client = HttpClient::create();

        $response    = $http_client->request('GET', $news_url);

        $news_html   = $response->getContent();

        $news_parser->parse($news_html, $news_count);

        return new Response('', 200);
    }
}
