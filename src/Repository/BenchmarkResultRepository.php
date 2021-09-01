<?php

namespace App\Repository;

use App\Entity\BenchmarkResult;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method BenchmarkResult|null find($id, $lockMode = null, $lockVersion = null)
 * @method BenchmarkResult|null findOneBy(array $criteria, array $orderBy = null)
 * @method BenchmarkResult[]    findAll()
 * @method BenchmarkResult[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BenchmarkResultRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BenchmarkResult::class);
    }

    // /**
    //  * @return BenchmarkResult[] Returns an array of BenchmarkResult objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('b.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?BenchmarkResult
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
