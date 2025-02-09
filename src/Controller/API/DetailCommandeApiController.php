<?php

namespace App\Controller\API;

use App\Entity\DetailCommande;
use App\Entity\Commande;
use App\Entity\Plat;
use App\Repository\DetailCommandeRepository;
use App\Enum\DetailStatu;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class DetailCommandeApiController extends AbstractController
{
    #[Route("/api/detailCommande", methods: ["POST"])]
    public function create(Request $request , EntityManagerInterface $em , SerializerInterface $serializer): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['idCommande']) || !isset($data['idPlat'])) {
            return $this->json(['message' => 'Plat ou commande ID manquant'], Response::HTTP_BAD_REQUEST);
        }
        
        $commande = $em->getRepository(Commande::class)->find($data['idCommande']);
        $plat = $em->getRepository(Plat::class)->find($data['idPlat']);

        if (!$commande || !$plat) {
            return $this->json(['message' => 'Plat ou commande non trouvé'], Response::HTTP_BAD_REQUEST);
        }

        $detailCommande = $serializer->deserialize(
            $request->getContent(),
            DetailCommande::class,
            'json',
            ['object_to_populate' => new DetailCommande()]
        );

        $detailCommande->setIdCommande($commande);
        $detailCommande->setIdPlat($plat);

        $em->persist($detailCommande);
        $em->flush();

        return $this->json($detailCommande, Response::HTTP_CREATED, [], [
            'groups' => ['detailcommande.show']
        ]);
    }

    #[Route("/api/detailsCommandeList", methods: ["GET"])]
    public function list(DetailCommandeRepository $repository, SerializerInterface $serializer): JsonResponse
    {
        $detailCommande = $repository->findAll();

        if (empty($detailCommande)) {
            return $this->json(['message' => 'Aucun ingredient trouvé'], Response::HTTP_NOT_FOUND);
        }

        return $this->json($detailCommande, Response::HTTP_OK, [], [
            'groups' => ['detailcommande.list']
        ]);
    }

    #[Route("/api/detailCommandeEdit/{id}", methods: ["PUT"])]
    public function edit(int $id,Request $request,DetailCommandeRepository $repository,EntityManagerInterface $em,SerializerInterface $serializer): JsonResponse 
    {
        $detailCommande = $repository->find($id);
        if (!$detailCommande) {
            throw new NotFoundHttpException('Plat non trouvé');
        }

        $serializer->deserialize(
            $request->getContent(),
            DetailCommande::class,
            'json',
            [AbstractNormalizer::OBJECT_TO_POPULATE => $detailCommande]
        );

        $em->persist($detailCommande);
        $em->flush();

        return $this->json($detailCommande, Response::HTTP_OK, [], [
            'groups' => ['detailcommande.show']
        ]);
    }    

    #[Route("/api/detailsCommandeByClient/{idClient}", methods: ["GET"])]
    public function listByClient(int $idClient, DetailCommandeRepository $repository, SerializerInterface $serializer): JsonResponse
    {
        $detailCommandes = $repository->createQueryBuilder('dc')
            ->join('dc.idCommande', 'c')
            ->where('c.idclient = :idclient')
            ->setParameter('idclient', $idClient)
            ->getQuery()
            ->getResult();

        if (empty($detailCommandes)) {
            return $this->json(['message' => 'Aucune commande trouvée pour ce client'], Response::HTTP_NOT_FOUND);
        }

        return $this->json($detailCommandes, Response::HTTP_OK, [], [
            'groups' => ['detailcommande.list']
        ]);
    }


}