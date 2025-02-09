<?php

namespace App\Controller\API;

use App\Entity\IngredientPlat;
use App\Entity\Plat;
use App\Entity\Ingredient;
use App\Repository\IngredientPlatRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Serializer\SerializerInterface;

class IngredientsPlatApiController extends AbstractController
{
    #[Route("/api/ingredientplat", methods: ["POST"])]
    public function create(Request $request, EntityManagerInterface $em): JsonResponse
    {
        // Décodage manuel du JSON
        $data = json_decode($request->getContent(), true);

        // Vérifier que les données attendues existent
        if (!isset($data['plat']) || !isset($data['ingredient'])) {
            return new JsonResponse(['error' => 'Payload invalide'], Response::HTTP_BAD_REQUEST);
        }

        // Récupération du plat existant à partir de l'ID fourni
        $plat = $em->getRepository(Plat::class)->find($data['plat']);
        if (!$plat) {
            return new JsonResponse(['error' => 'Plat introuvable'], Response::HTTP_BAD_REQUEST);
        }

        // Récupération de l'ingrédient existant à partir de l'ID fourni
        $ingredient = $em->getRepository(Ingredient::class)->find($data['ingredient']);
        if (!$ingredient) {
            return new JsonResponse(['error' => 'Ingredient introuvable'], Response::HTTP_BAD_REQUEST);
        }

        // Création et configuration de l'objet IngredientPlat
        $ingredientplat = new IngredientPlat();
        $ingredientplat->setPlat($plat);
        $ingredientplat->setIngredient($ingredient);

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


    // #[Route("/api/ingredientplat/{id}", methods: ["GET"])]
    // public function infoIngredientPlat(int $id,IngredientPlatRepository $repository, SerializerInterface $serializer): JsonResponse
    // {
    //     $ingredientDetail = $repository->find($id);

    //     return $this->json($ingredientDetail,200,[],[
    //         'groups' => ['ingredientPlat.show']]);
    // }


    #[Route("/api/ingredientplat/{id}", methods: ["PUT"])]
    public function edit(int $id, Request $request, IngredientPlatRepository $repository, EntityManagerInterface $em, SerializerInterface $serializer): JsonResponse 
    {
        $ingredientPlat = $repository->find($id);
        if (!$ingredientPlat) {
            throw new NotFoundHttpException('IngredientPlat non trouvé');
        }

        // Exemple de mise à jour : on peut modifier le plat et/ou l'ingrédient
        $data = json_decode($request->getContent(), true);
        if (isset($data['plat'])) {
            $plat = $em->getRepository(Plat::class)->find($data['plat']);
            if ($plat) {
                $ingredientPlat->setPlat($plat);
            }
        }
        if (isset($data['ingredient'])) {
            $ingredient = $em->getRepository(Ingredient::class)->find($data['ingredient']);
            if ($ingredient) {
                $ingredientPlat->setIngredient($ingredient);
            }
        }

        $em->persist($ingredientPlat);
        $em->flush();

        return $this->json($ingredientPlat, Response::HTTP_OK, [], [
            'groups' => ['ingredientPlat.show']
        ]);
    }
}
