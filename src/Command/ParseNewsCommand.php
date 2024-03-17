<?php

namespace App\Command;

use App\Factory\NewsParserFactory;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

#[AsCommand(
    name: 'parser:start',
    description: 'Add a short description for your command',
)]
class ParseNewsCommand extends Command
{
    private HttpClientInterface $httpClient;
    private NewsParserFactory $factory;
    private EntityManagerInterface $em;
    private array $sites;

    public function __construct(
        HttpClientInterface $httpClient,
        ParameterBagInterface $params,
        EntityManagerInterface $em,
        NewsParserFactory $factory,
    )
    {
        parent::__construct();
        $this->httpClient = $httpClient;
        $this->sites = $params->get('sites');
        $this->em = $em;
        $this->factory = new NewsParserFactory($em);
    }

    protected function configure(): void
    {
        $this
            ->addArgument('site', InputArgument::REQUIRED, 'The site to parse news from')
            ->addArgument('news_count', InputArgument::REQUIRED, 'The number of news to parse')
        ;
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ClientExceptionInterface
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $site = $input->getArgument('site');
        $newsCount = $input->getArgument('news_count');

        $newsUrl = $this->sites[$site] ?? null;

        if (is_null($newsUrl)) {
            $output->writeln('There are no code to parse this site at this time');
            return Command::FAILURE;
        }

        $newsParser = $this->factory->createParser($site);

        $response = $this->httpClient->request('GET', $newsUrl);
        $newsHtml = $response->getContent();

        $newsParser->parse($newsHtml, $newsCount);

        $output->writeln('News parsed successfully');

        return Command::SUCCESS;
    }
}