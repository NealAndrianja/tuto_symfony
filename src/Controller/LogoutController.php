<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

class LogoutController extends AbstractController
{
    #[Route('/logout', name: 'app_logout', methods: ['POST'])]
    public function logout(#[CurrentUser] ?User $user, EntityManagerInterface $entityManager): JsonResponse
    {
        if (null === $user) {
            return $this->json([
                'message' => 'No user is currently logged in',
            ], 400);
        }

        // Invalidate the user's API token by setting it to null
        $user->setApiToken(null);
        $entityManager->persist($user);
        $entityManager->flush();

        return $this->json([
            'message' => 'Logout successful',
        ]);
    }
}
