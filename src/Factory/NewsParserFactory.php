<?php
namespace App\Factory;

use App\Interface\ParserInterface;
use App\Parser\DefaultParser;
use App\Parser\RbcParser;
use Doctrine\ORM\EntityManagerInterface;

class NewsParserFactory
{
    public EntityManagerInterface $entityManager;


    public function __construct(
        EntityManagerInterface $entityManager,
    )
    {
        $this->entityManager = $entityManager;
    }

    public function createParser(string $site): ParserInterface
    {
        return match ($site) {
            'rbc' => new RbcParser(
                '//div[@class="js-news-feed-item js-yandex-counter"]',
                $this->entityManager,
                $site

            ),
            default => new DefaultParser(
                '',
                $this->entityManager,
                $site
            ),
        };
    }
}
