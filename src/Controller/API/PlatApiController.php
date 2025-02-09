<?php

namespace App\Controller\API;

use App\Entity\Plat;

use App\Repository\PlatRepository;

use App\Service\DeleteService;
use App\Service\FileUploadService;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class PlatApiController extends AbstractController
{
    #[Route("/api/plats", methods: ["POST"])]
    public function create(Request $request, EntityManagerInterface $em, SerializerInterface $serializer,FileUploadService $fileUploadService): JsonResponse
    {

        $plat = new Plat();

        $nomPlat = $request->request->get('nomPlat');
        $prixUnitaire = $request->request->get('prixUnitaire');
        $plat->setPrixUnitaire($prixUnitaire);
        $tempsCuisson = $request->request->get('tempsCuisson'); // Exemple: '00:30:00'

        if ($tempsCuisson) {
            try {
                $dateTime = new \DateTime($tempsCuisson); // Convertit le temps en DateTime
                $plat->setTempsCuisson($dateTime);
            } catch (\Exception $e) {
                // Gérer l'erreur si la conversion échoue
                throw new \InvalidArgumentException("Temps de cuisson invalide.");
            }
        }

        if ($nomPlat) {
            $plat->setNomPlat($nomPlat);
        } else {
            return $this->json(['detail' => 'Le champ nomPlat est requis'], Response::HTTP_BAD_REQUEST);
        }

        $file = $request->files->get('image');
        if ($file) {
            $filename = $fileUploadService->upload($file);
            $plat->setImage($filename);
        }

        $em->persist($plat);
        $em->flush();
        // Désérialisation de la requête en objet Plat
        // $plat = $serializer->deserialize(
        //     $request->getContent(),
        //     Plat::class,
        //     'json'
        // );

        // $em->persist($plat);
        // $em->flush();

        return $this->json($plat, Response::HTTP_CREATED, [], [
            'groups' => ['plats.show']
        ]);
    }

    #[Route("/api/plats", methods: ["GET"])]
    public function list(PlatRepository $repository, SerializerInterface $serializer): JsonResponse
    {
        $platlist = $repository->findAll();

        if (empty($platlist)) {
            return $this->json(['message' => 'Aucun plat trouvé'], Response::HTTP_NOT_FOUND);
        }

        return $this->json($platlist, Response::HTTP_OK, [], [
            'groups' => ['plats.list']
        ]);
    }


    #[Route("/api/plats/{id}", methods: ["PUT"])]
    public function edit(
        int $id,
        Request $request,
        PlatRepository $repository,
        EntityManagerInterface $em,
        SerializerInterface $serializer
    ): JsonResponse {
        $plat = $repository->find($id);
        if (!$plat) {
            throw new NotFoundHttpException('Plat non trouvé');
        }

        $serializer->deserialize(
            $request->getContent(),
            Plat::class,
            'json',
            [AbstractNormalizer::OBJECT_TO_POPULATE => $plat]
        );

        $em->persist($plat);
        $em->flush();

        return $this->json($plat, Response::HTTP_OK, [], [
            'groups' => ['plats.show']
        ]);
    }

    #[Route("/api/plats/{id}", methods: ["DELETE"])]
    public function delete( int $id,DeleteService $deleteService,PlatRepository $repository,)
    {
        // Récupérer le projet existant
        $plat = $repository->find($id);
        if (!$plat) {
            throw new NotFoundHttpException('Projet non trouvé');
        }

        // Delete plat
        $deleteService->softDelete($plat);

        // Return no content code
        return new Response(null, 204);
    }
}
