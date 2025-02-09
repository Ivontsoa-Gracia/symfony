<?php

namespace App\Controller\API;

use App\Entity\IngredientPlat;
use App\Entity\Plat;
use App\Entity\Ingredient;
use App\Repository\IngredientPlatRepository;
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

class IngredientsPlatApiController extends AbstractController
{
   #[Route("/api/ingredientplat", methods: ["POST"])]
    public function create(Request $request, EntityManagerInterface $em, SerializerInterface $serializer): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        //Vérifier si les IDs sont présents dans la requête
        if (!isset($data['plat']) || !isset($data['ingredient'])) {
            return $this->json(['message' => 'Plat ou ingrédient ID manquant'], Response::HTTP_BAD_REQUEST);
        }

        // Récupérer les entités Plat et Ingredient par leurs IDs
        $plat = $em->getRepository(Plat::class)->find($data['plat']);
        $ingredient = $em->getRepository(Ingredient::class)->find($data['ingredient']);

        if (!$plat || !$ingredient) {
            return $this->json(['message' => 'Plat ou ingrédient non trouvé'], Response::HTTP_BAD_REQUEST);
        }

        // Désérialisation de l'objet IngredientPlat (sans les relations)
        $ingredientplat = $serializer->deserialize(
            $request->getContent(),
            IngredientPlat::class,
            'json',
            ['object_to_populate' => new IngredientPlat()]
        );

        // Associer les entités Plat et Ingredient à IngredientPlat
        $ingredientplat->setPlat($plat);
        $ingredientplat->setIngredient($ingredient);

        // Persister et sauvegarder
        $em->persist($ingredientplat);
        $em->flush();

        return $this->json($ingredientplat, Response::HTTP_CREATED, [], [
            'groups' => ['ingredientPlat.show']
        ]);
    }

    #[Route("/api/ingredientplat", methods: ["GET"])]
    public function list(IngredientPlatRepository $repository, SerializerInterface $serializer): JsonResponse
    {
        $ingredientplatlist = $repository->findAll();

        if (empty($ingredientplatlist)) {
            return $this->json(['message' => 'Aucun ingredient trouvé'], Response::HTTP_NOT_FOUND);
        }

        return $this->json($ingredientplatlist, Response::HTTP_OK, [], [
            'groups' => ['ingredientPlat.list']
        ]);
    }

    #[Route("/api/ingredientPlat/{id}", methods: ["PUT"])]
    public function edit(int $id,Request $request,IngredientPlatRepository $repository,EntityManagerInterface $em,SerializerInterface $serializer): JsonResponse 
    {
        $ingredientPlat = $repository->find($id);
        if (!$ingredient) {
            throw new NotFoundHttpException('Plat non trouvé');
        }

        $serializer->deserialize(
            $request->getContent(),
            Plat::class,
            'json',
            [AbstractNormalizer::OBJECT_TO_POPULATE => $ingredient]
        );

        $em->persist($ingredient);
        $em->flush();

        return $this->json($ingredient, Response::HTTP_OK, [], [
            'groups' => ['ingredient.show']
        ]);
    }

}
