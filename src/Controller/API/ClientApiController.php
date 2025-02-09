<?php

namespace App\Controller\API;

use App\Entity\Ingredient;

use App\Repository\IngredientRepository;
use App\Repository\StockRepository;
use App\Service\FileUploadService;
use App\Service\DeleteService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ClientApiController extends AbstractController
{
    #[Route("/api/ingredients", methods: ["POST"])]
    public function create(
        Request $request,
        EntityManagerInterface $em,
        FileUploadService $fileUploadService
    ): JsonResponse {
        $ingredient = new Ingredient();

        // Récupération des données textuelles
        $nomIngredient = $request->request->get('nomIngredient');
        if ($nomIngredient) {
            $ingredient->setNomIngredient($nomIngredient);
        } else {
            return $this->json(['detail' => 'Le champ nomIngredient est requis'], Response::HTTP_BAD_REQUEST);
        }

        // Gestion du fichier image
        $file = $request->files->get('image');
        if ($file) {
            $filename = $fileUploadService->upload($file);
            $ingredient->setImage($filename);
        }

        // Sauvegarde de l'ingrédient en base de données
        $em->persist($ingredient);
        $em->flush();

        return $this->json($ingredient, Response::HTTP_CREATED, [], [
            'groups' => ['ingredient.show']
        ]);
    }


    #[Route("/api/ingredients/list", methods: ["GET"])]
    public function list(IngredientRepository $repository): JsonResponse
    {
        $ingredientlist = $repository->findAll();

        if (empty($ingredientlist)) {
            return $this->json(['message' => 'Aucun ingrédient trouvé'], Response::HTTP_NOT_FOUND);
        }

        return $this->json($ingredientlist, Response::HTTP_OK, [], [
            'groups' => ['ingredient.list']
        ]);
    }

    #[Route("/api/ingredients", methods: ["GET"])]
    public function listIngredientStock(
        IngredientRepository $repository,
        StockRepository $stockRepository
    ): JsonResponse {
        $ingredients = $repository->findAll();

        if (empty($ingredients)) {
            return $this->json(['message' => 'Aucun ingrédient trouvé'], Response::HTTP_NOT_FOUND);
        }

        $ingredientData = [];
        foreach ($ingredients as $ingredient) {
            $remainingStock = $stockRepository->getRemainingStock($ingredient);
            $ingredientData[] = [
                'id' => $ingredient->getId(),
                'nomIngredient' => $ingredient->getNomIngredient(),
                'image' => $ingredient->getImage(),
                'remainingStock' => $remainingStock,
            ];
        }

        return $this->json($ingredientData, Response::HTTP_OK);
    }


    #[Route("/api/ingredients/{id}", methods: ["PUT"])]
    public function edit(
        int $id,
        Request $request,
        IngredientRepository $repository,
        EntityManagerInterface $em,
        SerializerInterface $serializer,
        FileUploadService $fileUploadService
    ): JsonResponse {
        $ingredient = $repository->find($id);
        if (!$ingredient) {
            throw new NotFoundHttpException('Ingrédient non trouvé');
        }

        // Désérialisation et mise à jour de l'objet
        $serializer->deserialize(
            $request->getContent(),
            Ingredient::class,
            'json',
            [AbstractNormalizer::OBJECT_TO_POPULATE => $ingredient]
        );

        // Vérification et gestion du fichier image si une nouvelle image est fournie
        $file = $request->files->get('image');
        if ($file) {
            // Suppression de l'ancienne image
            if ($ingredient->getImage()) {
                $fileUploadService->delete($ingredient->getImage());
            }

            // Upload de la nouvelle image
            $filename = $fileUploadService->upload($file);
            $ingredient->setImage($filename);
        }

        $em->persist($ingredient);
        $em->flush();

        return $this->json($ingredient, Response::HTTP_OK, [], [
            'groups' => ['ingredient.show']
        ]);
    }

    #[Route("/api/ingredient/{id}", methods: ["DELETE"])]
    public function delete(int $id,DeleteService $deleteService,IngredientRepository $repository)
    {
        // Récupérer l'ingredient existant
        $ingredient = $repository->find($id);
        if (!$ingredient) {
            throw new NotFoundHttpException('Ingredient non trouvé');
        }

        // Delete ingredient
        // $deleteService->hardDelete($ingredient);

        // Return no content code
        return new Response(null, 204);
    }
}
