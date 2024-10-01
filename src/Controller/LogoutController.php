<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\ApiTokenRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;

class LogoutController extends AbstractController
{
    #[Route('/logout', name: 'app_logout', methods: ['POST'])]
    public function logout(Request $request, EntityManagerInterface $entityManager, ApiTokenRepository $apiTokenRepository): JsonResponse
    {
        if (!$request->headers->has('Authorization') || !str_contains($request->headers->get('Authorization'), 'Bearer')) {
            return $this->json([
                'message' => 'No user is currently logged in',
            ], 400);
        }

        $token = str_replace('Bearer ', '', $request->headers->get('Authorization'));

        $apiToken = $apiTokenRepository->findOneBySomeField($token);
        if (!$apiToken) {
            return $this->json([
                'message' => 'Invalid API token',
            ], 401);
        }

        // Delete the API token from the database
        $entityManager->remove($apiToken);
        $entityManager->flush();

        return $this->json([
            'message' => 'Logout successful',
        ]);

    }
}
