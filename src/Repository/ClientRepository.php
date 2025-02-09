<?php

namespace App\Repository;

use App\Entity\Client;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Client>
 */
class ClientRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Client::class);
    }


    // public function findActiveUsers(bool $isAdmin, string $search = ''): array
    // {
    //     $qb = $this->createQueryBuilder('u');

    //     $query = $this->createQueryWithFilters($qb, $isAdmin, $search);
    //     return $query->getResult();
    // }

    // private function createQuery(QueryBuilder $qb): Query
    // {
    //     return $qb->getQuery();
    // }

    // private function createQueryWithFilters(QueryBuilder $qb, string $search = ''): Query
    // {

    //     // Rechercher par titre (si renseigné)
    //     if ($search) {
    //         $qb->andWhere('(u.username LIKE :search or u.email like :search)')
    //             ->setParameter('title', '%' . $search . '%');
    //     }

    //     return $qb->getQuery();
    // }

   /**
    * @return Client[] Returns an array of Client objects
    */
   public function findByExampleField($value): array
   {
       return $this->createQueryBuilder('c')
           ->andWhere('c.email = :val')
           ->setParameter('val', $value)
           ->orderBy('c.id', 'ASC')
           ->setMaxResults(10)
           ->getQuery()
           ->getResult()
       ;
   }

    public function findOneBySomeField($mail,$mot): ?Client
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.email = :val, c.mdp= :mdp')
            ->setParameter('val', $value)
            ->setParameter('mdp', $mot)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
}
