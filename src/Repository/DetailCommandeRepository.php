<?php

namespace App\Repository;

use App\Entity\DetailCommande;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<DetailCommande>
 */
class DetailCommandeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DetailCommande::class);
    }

    /**
     * Récupère la liste des détails de commande avec les informations de la commande, du client et du plat.
     *
     * @return array Tableau associatif des détails de commande
     */
    public function getDetailCommandeList(): array
    {
        $qb = $this->createQueryBuilder('d')
            ->select(
                'd.id AS detail_id, ' .
                'c.id AS commande_id, ' .
                'c.dateCommande AS date_commande, ' .
                'p.nomPlat AS plat_nom, ' .
                'cl.email AS client_email, ' .
                'd.status AS detail_status'
            )
            ->join('d.idCommande', 'c')
            ->join('c.idclient', 'cl')
            ->join('d.idPlat', 'p')
            ->orderBy('d.id', 'ASC');

        return $qb->getQuery()->getArrayResult();
    }

    // nombre de plat servi
    public function nobrePlatServi(): int
    {
        $req = $this->createQueryBuilder('d')
            ->select('SUM(d.quantite) AS totalQuantite');
            // ->select('p.id AS idPlat, p.nomPlat, SUM(d.quantite) AS totalQuantite')
            // ->join('d.idPlat', 'p')
            // ->groupBy('p.id, p.nomPlat')
            // ->orderBy('totalQuantite', 'DESC');  // Tri par quantité servie (optionnel)

        // return $req->getQuery()->getArrayResult();
        $result = $req->getQuery()->getSingleScalarResult();
        return (int) ($result ?? 0);
    }


    public function getDailySales(): array
    {
        $qb = $this->createQueryBuilder('d')
            ->select(
                "SUBSTRING(c.dateCommande, 1, 10) as saleDate, 
                p.id as platId, 
                p.nomPlat as platName, 
                COUNT(p.id) as totalQuantity, 
                SUM(p.id * p.prixUnitaire) as totalAmount"
            )
            ->join('d.idCommande', 'c')
            ->join('d.idPlat', 'p')
            ->groupBy('saleDate')
            ->orderBy('saleDate', 'ASC');

        return $qb->getQuery()->getArrayResult();
    }



//    /**
//     * @return DetailCommande[] Returns an array of DetailCommande objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('d')
//            ->andWhere('d.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('d.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?DetailCommande
//    {
//        return $this->createQueryBuilder('d')
//            ->andWhere('d.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
