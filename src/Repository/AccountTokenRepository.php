<?php

namespace App\Repository;

use App\Entity\AccountToken;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<AccountToken>
 *
 * @method AccountToken|null find($id, $lockMode = null, $lockVersion = null)
 * @method AccountToken|null findOneBy(array $criteria, array $orderBy = null)
 * @method AccountToken[]    findAll()
 * @method AccountToken[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AccountTokenRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AccountToken::class);
    }

//    /**
//     * @return AccountToken[] Returns an array of AccountToken objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('a.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?AccountToken
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
