<?php

namespace App\Controller\API;

use App\Entity\Client;
use App\Repository\ClientRepository;
use App\Service\JwtTokenManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Routing\Requirement\Requirement;


class ClientApiController extends AbstractController
{
    private $jwtTokenManager;

    public function __construct(JwtTokenManager $jwtTokenManager)
    {
        $this->jwtTokenManager = $jwtTokenManager;
    }

    #[Route("/api/clients", methods: ["POST"])]
    // #[TokenRequired]
    public function create(Request $request, SerializerInterface $serializer, ValidatorInterface $validator, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $em): JsonResponse 
    {
        // Décoder la requête JSON en objet Client
        $client = $serializer->deserialize($request->getContent(), Client::class, 'json');

        // Valider l'entité
        $errors = $validator->validate($client);
        if (count($errors) > 0) {
            return new JsonResponse(['error' => (string) $errors], 400);
        }

        // Vérifier que le mot de passe est présent
        if (!$client->getPassword()) {
            return new JsonResponse(['error' => 'Mot de passe requis'], 400);
        }

        // Hacher le mot de passe
        $client->setPassword($userPasswordHasher->hashPassword($client, $client->getPassword()));

        // Sauvegarder en base de données
        $em->persist($client);
        $em->flush();

        // Retourner la réponse
        return $this->json($client, 201, [], ['groups' => ['client.show']]);
    }


    #[Route("/api/clients/findByEmail", methods: "POST")]
    public function findClientByEmail(Request $request, ClientRepository $clientRepository): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $email = $data['email'] ?? '';

        // Appel à la méthode du ClientRepository pour trouver le client par email
        $clients = $clientRepository->findByExampleField($email);

        // Vérification si le client existe
        if (empty($clients)) {
            return new JsonResponse(['message' => 'Client non trouvé'], Response::HTTP_NOT_FOUND);
        }

        // Supposons que le premier résultat est le client recherché
        $client = $clients[0];

        return new JsonResponse(['id' => $client->getId()], Response::HTTP_OK);
    }


    #[Route("/api/clients/{id}", methods: "GET", requirements: ['id' => Requirement::DIGITS])]
    // #[TokenRequired]
    public function findById(Client $client)
    {
        return $this->json($client, 200, [], [
            'groups' => ['client.show']
        ]);
    }

    #[Route("/api/clients/{id}", methods: "PUT")]
    #[TokenRequired]
    public function update(int $id, Request $request, ClientRepository $repository, EntityManagerInterface $em, SerializerInterface $serializer): JsonResponse 
    {
        // Récupérer le client existant
        $client = $repository->find($id);
        if (!$client) {
            throw new NotFoundHttpException('Client non trouvé');
        }

        // Désérialisation partielle
        $updatedClient = $serializer->deserialize(
            $request->getContent(),
            Client::class,
            'json',
            [AbstractNormalizer::OBJECT_TO_POPULATE => $client, 'groups' => ['client.update']]
        );

        // Si un nouveau mot de passe est fourni, le hacher
        if ($updatedClient->getPassword()) {
            $updatedClient->setPassword($userPasswordHasher->hashPassword($updatedClient, $updatedClient->getPassword()));
        }

        // Sauvegarder les modifications
        $em->persist($updatedClient);
        $em->flush();

        return $this->json($updatedClient, 200, [], ['groups' => ['client.show']]);
    }

    #[Route("/api/clients/{id}", methods: "DELETE")]
    #[TokenRequired]
    public function delete(int $id, DeleteService $deleteService, ClientRepository $repository): Response 
    {
        $client = $repository->find($id);
        if (!$client) {
            throw new NotFoundHttpException('Client non trouvé');
        }

        $deleteService->softDelete($client);

        return new Response(null, 204);
    }

    #[Route("/api/clients/login", methods: "POST")]
    public function login(Request $request, ClientRepository $repository,  UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $em)
    {
        $data = json_decode($request->getContent(), true);
        $email = $data['email'] ?? '';
        $password = $data['password'] ?? '';

        $client = $repository->findOneBy(['email' => $email]);
        if (!$client || !$userPasswordHasher->isPasswordValid($client, $password)) {
            return new JsonResponse(['message' => 'Invalid credentials'], Response::HTTP_UNAUTHORIZED);
        }

        $claims = [
            'clientId' => $client->getId(),
        ];
        $token = $this->jwtTokenManager->createToken($claims, 3600);

        // Generate token and update database
        $client->setApiToken($token->toString());
        $em->persist($client);
        $em->flush();

        return new JsonResponse(['token' => $client->getApiToken()]);
    }
}
