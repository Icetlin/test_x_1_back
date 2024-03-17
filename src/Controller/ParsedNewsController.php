<?php

namespace App\Controller;


use App\Repository\ParsedNewsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ParsedNewsController extends AbstractController
{
    /**
     * @var EntityManagerInterface
     */
    public EntityManagerInterface $em;

    /**
     * @var ParsedNewsRepository
     */
    public ParsedNewsRepository $parsedNewsRepository;


    public function __construct(
        EntityManagerInterface $em,
        ParsedNewsRepository $parsedNewsRepository
    )
    {
        $this->em = $em;
        $this->parsedNewsRepository = $parsedNewsRepository;
    }

    #[Route(
        '/api/parsed_news/{source_site}',
        name: 'getNews',
        methods: ['GET']
    )]
    public function getNews(
        Request $request,
        string $source_site,
        int $pageSize = 15,
        int $page = 1,
        string $sort = 'id',
        string $sortOrder = 'desc',
    ): Response
    {
        $pageSize = $request->query->getInt('page_size') ?? $pageSize;
        $page = $request->query->getInt('page') ?? $page;
        $sort = $request->query->get('sort') ?? $sort;
        $sortOrder = $request->query->get('sort_order') ?? $sortOrder;

        $allowedSortFields = ['id', 'name', 'rating'];
        $allowedSortOrders = ['desc', 'asc'];

        if (!in_array($sort, $allowedSortFields, true)) {
            $sort = 'id';
        }
        if (!in_array($sortOrder, $allowedSortOrders, true)) {
            $sortOrder = 'desc';
        }

        $queryBuilder = $this->parsedNewsRepository->createQueryBuilder('n')
            ->where('n.source_site = :source_site')
            ->setParameter('source_site', $source_site)
            ->orderBy('n.' . $sort, $sortOrder)
            ->setMaxResults($pageSize)
            ->setFirstResult(($page - 1) * $pageSize);

        $news = $queryBuilder->getQuery()->getResult();

        $newsArray = [];
        foreach ($news as $oneNews) {
            $oneNewsArray = [];
            $oneNewsArray['id'] = $oneNews->getId();
            $oneNewsArray['name'] = $oneNews->getName();
            $oneNewsArray['image_url'] = $oneNews->getSourceUrl();
            $oneNewsArray['news_url'] = $oneNews->getUrl();
            $oneNewsArray['rating'] = $oneNews->getRating();
            $oneNewsArray['value'] = $oneNews->getValue();
            $oneNewsArray['source_site'] = $oneNews->getSourceSite();
            $newsArray[] = $oneNewsArray;
        }

        $newsArrayJson = json_encode($newsArray, JSON_PARTIAL_OUTPUT_ON_ERROR | JSON_UNESCAPED_UNICODE);

        return new Response($newsArrayJson, 200);
    }

    #[Route(
        '/api/parsed_news/{id}',
        name: 'deleteNews',
        methods: ['DELETE']
    )]
    public function deleteNews(
        Request $request,
        int $id,
        ParsedNewsRepository $parsedNewsRepository
    ): Response
    {
        $news = $parsedNewsRepository->find($id);

        if (!$news) {
            return new Response('News not found', Response::HTTP_NOT_FOUND);
        }

        $this->em->remove($news);
        $this->em->flush();

        return new Response('', Response::HTTP_OK);
    }
}
