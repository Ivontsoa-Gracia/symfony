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
   
}
