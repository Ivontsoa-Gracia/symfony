<?php

namespace App\Controller\API;

use App\Entity\Stock;
use App\Entity\Ingredient;
use App\Enum\StockStatu;

use App\Repository\IngredientRepository;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class StockApiController extends AbstractController
{
    #[Route('/api/stock/create', name: 'api_stock_create', methods: ['POST'])]
    public function createStock(Request $request, EntityManagerInterface $entityManager, SerializerInterface $serializer): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        // Vérification des données
        if (!isset($data['quantite'], $data['dateMouvement'], $data['idIngredient'], $data['status'])) {
            return new JsonResponse(['error' => 'Missing required fields'], 400);
        }

        // Récupérer l'ingrédient correspondant à idIngredient
        $ingredient = $entityManager->getRepository(Ingredient::class)->find($data['idIngredient']);
        if (!$ingredient) {
            return new JsonResponse(['error' => 'Ingredient not found'], 404);
        }

        // Créer une nouvelle instance de Stock
        $stock = new Stock();
        $stock->setQuantite($data['quantite']);
        $stock->setDateMouvement(new \DateTime($data['dateMouvement']));
        $stock->setIdIngredient($ingredient);

        // Vérification et attribution du statut
        if (!in_array($data['status'], array_map(fn($case) => $case->value, StockStatu::cases()))) {
            return new JsonResponse(['error' => 'Invalid status'], 400);
        }
        $stock->setStatus(StockStatu::from($data['status']));        

        // Sauvegarder dans la base de données
        $entityManager->persist($stock);
        $entityManager->flush();

        // Retourner la réponse JSON
        return new JsonResponse(
            $serializer->normalize($stock, null, ['groups' => 'stock.create']),
            201
        );
    }

    #[Route('/api/stock/remaining/{id}', name: 'api_stock_remaining', methods: ['GET'])]
    public function getRemainingStock(int $id, EntityManagerInterface $entityManager): JsonResponse
    {
        $ingredient = $entityManager->getRepository(Ingredient::class)->find($id);

        if (!$ingredient) {
            return new JsonResponse(['error' => 'Ingredient not found'], 404);
        }

        $remainingStock = $entityManager->getRepository(Stock::class)->getRemainingStock($ingredient);

        return new JsonResponse(['ingredientId' => $id, 'remainingStock' => $remainingStock]);
    }

    

}