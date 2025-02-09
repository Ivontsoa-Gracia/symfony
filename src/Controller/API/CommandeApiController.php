<?php

namespace App\Controller\API;

use App\Entity\Commande;
use App\Entity\Client;
use App\Repository\CommandeRepository;
use App\Enum\StatuCommande;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;


class CommandeApiController extends AbstractController
{
    #[Route("/api/commandes", methods: ["POST"])]
    public function create(Request $request, EntityManagerInterface $em, SerializerInterface $serializer): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        // Vérification si l'ID du client est bien envoyé
        if (!isset($data['idclient'])) {
            return $this->json(['error' => 'idclient is required'], Response::HTTP_BAD_REQUEST);
        }

        // Récupération du client depuis la base de données
        $client = $em->getRepository(Client::class)->find($data['idclient']);
        
        if (!$client) {
            return $this->json(['error' => 'Client not found'], Response::HTTP_NOT_FOUND);
        }

        // Création de la commande
        $commande = new Commande();

        
        $commande->setIdclient($client);
        $commande->setDateCommande(new \DateTime($data['dateCommande']));
        $commande->setMontantTotal($data['montantTotal']);
        $commande->setStatus(StatuCommande::from($data['status']));

        $em->persist($commande);
        $em->flush();

        return $this->json($commande, Response::HTTP_CREATED, [], [
            'groups' => ['commande.show']
        ]);
    }

    #[Route("/api/commandes", methods: ["GET"])]
    public function list(CommandeRepository $repository, SerializerInterface $serializer): JsonResponse
    {
        $commande = $repository->findAll();

        if (empty($commande)) {
            return $this->json(['message' => 'Aucun commande trouvé'], Response::HTTP_NOT_FOUND);
        }

        return $this->json($commande, Response::HTTP_OK, [], [
            'groups' => ['commande.list']
        ]);
    }

    #[Route("/api/commandes/{id}", methods: ["PUT"])]
    public function edit(int $id,Request $request,CommandeRepository $repository,EntityManagerInterface $em,SerializerInterface $serializer): JsonResponse 
    {
        $commande = $repository->find($id);
        if (!$commande) {
            throw new NotFoundHttpException('Plat non trouvé');
        }

        $serializer->deserialize(
            $request->getContent(),
            Commande::class,
            'json',
            [AbstractNormalizer::OBJECT_TO_POPULATE => $commande]
        );

        $em->persist($commande);
        $em->flush();

        return $this->json($commande, Response::HTTP_OK, [], [
            'groups' => ['commande.show']
        ]);
    }



}



