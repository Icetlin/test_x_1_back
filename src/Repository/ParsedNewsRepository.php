<?php

namespace App\Repository;

use App\Entity\ParsedNews;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ParsedNews>
 *
 * @method ParsedNews|null find($id, $lockMode = null, $lockVersion = null)
 * @method ParsedNews|null findOneBy(array $criteria, array $orderBy = null)
 * @method ParsedNews[]    findAll()
 * @method ParsedNews[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ParsedNewsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ParsedNews::class);
    }

    //    /**
    //     * @return ParsedNews[] Returns an array of ParsedNews objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('p.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?ParsedNews
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
