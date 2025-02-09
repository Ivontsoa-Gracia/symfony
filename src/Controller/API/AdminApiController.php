<?php

namespace App\Controller\API;

use App\Entity\Admin;

use App\Repository\AdminRepository;
use App\Repository\DetailCommandeRepository;
use App\Repository\StockRepository;
use App\Repository\Repository;



use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

class AdminApiController extends AbstractController
{
    #[Route('/admin/login', name: 'login_admin', methods: ['POST'])]
    public function loginAdmin(Request $request, AdminRepository $adminRepository): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return new JsonResponse(['error' => 'Invalid JSON input.'], JsonResponse::HTTP_BAD_REQUEST);
        }

        $email = $data['email'] ?? null;
        $motPasse = $data['mdp'] ?? null;

        if (!$email || !$motPasse) {
            return new JsonResponse(['error' => 'Email et mot de passe sont requis.'], JsonResponse::HTTP_BAD_REQUEST);
        }

        $admin = $adminRepository->verificationCompte($email, $motPasse);

        if ($admin) {
            return new JsonResponse(['success' => true, 'message' => 'Connexion réussie !']);
        }

        return new JsonResponse(['success' => false, 'message' => 'Identifiants incorrects.'], JsonResponse::HTTP_UNAUTHORIZED);
    }

    #[Route('/admin/state', name: 'statistique', methods: ['GET'])]
    public function statistique(DetailCommandeRepository $detailrepo, StockRepository $stockrepo): JsonResponse
    {
        $platServi = $detailrepo->nobrePlatServi();
        $totalVente = $stockrepo->montantTotalVente();

        $data = [
            'platServi' => $platServi,
            'montant'   => $totalVente,
        ];

        return $this->json($data);
    }

    #[Route('/api/vente', name: 'api_sales_daily', methods: ['GET'])]
    public function getDailySales(DetailCommandeRepository $detailCommandeRepository): JsonResponse
    {
        $data = $detailCommandeRepository->getDailySales();
        return $this->json($data);
    }
}