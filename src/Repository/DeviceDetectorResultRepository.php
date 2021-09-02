<?php

namespace App\Repository;

use App\Entity\DeviceDetectorResult;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method DeviceDetectorResult|null find($id, $lockMode = null, $lockVersion = null)
 * @method DeviceDetectorResult|null findOneBy(array $criteria, array $orderBy = null)
 * @method DeviceDetectorResult[]    findAll()
 * @method DeviceDetectorResult[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DeviceDetectorResultRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DeviceDetectorResult::class);
    }

    // /**
    //  * @return DeviceDetectorResult[] Returns an array of DeviceDetectorResult objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('m.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?DeviceDetectorResult
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
