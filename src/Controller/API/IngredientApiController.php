<?php

namespace App\Controller\API;

use App\Entity\Ingredient;
use App\Repository\IngredientRepository;
use App\Repository\StockRepository;
use App\Service\DeleteService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class IngredientApiController extends AbstractController
{

    private StockRepository $stockRepository;

    // Utilise l'injection de dépendance pour StockRepository
    public function __construct(StockRepository $stockRepository)
    {
        $this->stockRepository = $stockRepository;
    }

    #[Route("/api/ingredients", methods: ["POST"])]
    public function create(Request $request, EntityManagerInterface $em, SerializerInterface $serializer): JsonResponse
    {
        // Désérialisation de la requête en objet Plat
        $ingredient = $serializer->deserialize(
            $request->getContent(),
            Ingredient::class,
            'json'
        );

        $em->persist($ingredient);
        $em->flush();

        return $this->json($ingredient, Response::HTTP_CREATED, [], [
            'groups' => ['ingredient.show']
        ]);
    }

    #[Route("/api/ingredients", methods: ["GET"])]
    public function list(IngredientRepository $repository, SerializerInterface $serializer): JsonResponse
    {
        $ingredientlist = $repository->findAll();

        if (empty($ingredientlist)) {
            return $this->json(['message' => 'Aucun ingredient trouvé'], Response::HTTP_NOT_FOUND);
        }

        return $this->json($ingredientlist, Response::HTTP_OK, [], [
            'groups' => ['ingredient.list']
        ]);
    }

    #[Route("/api/ingredients/{id}", methods: ["PUT"])]
    public function edit(int $id,Request $request,IngredientRepository $repository,EntityManagerInterface $em,SerializerInterface $serializer): JsonResponse 
    {
        $ingredient = $repository->find($id);
        if (!$ingredient) {
            throw new NotFoundHttpException('Plat non trouvé');
        }

        $serializer->deserialize(
            $request->getContent(),
            Ingredient::class,
            'json',
            [AbstractNormalizer::OBJECT_TO_POPULATE => $ingredient]
        );

        $em->persist($ingredient);
        $em->flush();

        return $this->json($ingredient, Response::HTTP_OK, [], [
            'groups' => ['ingredient.show']
        ]);
    }


    #[Route("/api/plats/{id}", methods: ["DELETE"])]
    public function delete( int $id,DeleteService $deleteService,IngredientRepository $repository,)
    {
        // Récupérer le projet existant
        $ingredient = $repository->find($id);
        if (!$ingredient) {
            throw new NotFoundHttpException('Projet non trouvé');
        }

        // Delete plat
        $deleteService->softDelete($ingredient);

        // Return no content code
        return new Response(null, 204);
    }

     // Méthode pour récupérer la liste des ingrédients et leur stock restant
    // IngredientApiController.php
    #[Route("/api/ingredients", methods: ["GET"])]
    public function listIngredientStock(IngredientRepository $repository): JsonResponse
    {
        $ingredients = $repository->findAll();

        if (empty($ingredients)) {
            return $this->json(['message' => 'Aucun ingrédient trouvé'], Response::HTTP_NOT_FOUND);
        }

        $ingredientData = [];
        foreach ($ingredients as $ingredient) {
            // $remainingStock = $this->stockRepository->findByIngredientId($ingredient);
            $ingredientData[] = [
                'id' => $ingredient->getId(),
                'nomIngredient' => $ingredient->getNomIngredient(),
                'image' => $ingredient->getImage(),
                'remainingStock' => $remainingStock,
            ];
        }

        return $this->json($ingredientData, Response::HTTP_OK);
    }

}

