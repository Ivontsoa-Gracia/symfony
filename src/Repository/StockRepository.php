<?php

namespace App\Repository;

use App\Entity\Ingredient;
use App\Entity\Stock;
use App\Enum\StockStatu;
use App\Enum\DetailStatu;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class StockRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Stock::class);
    }

    public function getRemainingStock(Ingredient $ingredient): int
    {
        $qb = $this->createQueryBuilder('s');
        
        $qb->select(
            'SUM(CASE WHEN s.status = :entree THEN s.quantite ELSE 0 END) - SUM(CASE WHEN s.status = :sortie THEN s.quantite ELSE 0 END) AS remainingStock'
        )
        ->where('s.idIngredient = :ingredient')
        ->setParameter('entree', StockStatu::ENTREE)
        ->setParameter('sortie', StockStatu::SORTIE)
        ->setParameter('ingredient', $ingredient);

        $result = $qb->getQuery()->getSingleScalarResult();

        return $result ? (int) $result : 0;
    }

    public function montantTotalVente(): float
    {
        $qb = $this->createQueryBuilder('dc')
            ->select('SUM(dc.quantite * p.prixUnitaire) AS totalVente')
            ->join('dc.idPlat', 'p')
            ->where('dc.status = :status')
            ->setParameter('status', DetailStatu::RECUPERER) // Ajuste ce statut si nécessaire
            ->getQuery();

        $result = $qb->getSingleScalarResult();
        
        return $result ? (float) $result : 0.0;
    }

}
