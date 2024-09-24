<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

class ApiLoginController extends AbstractController
{
    #[Route('/login', name: 'app_login', methods: ['POST'])]
    public function login(
        Request $request, 
        EntityManagerInterface $entityManager, 
        UserPasswordHasherInterface $passwordHasher
    ): JsonResponse 
    {
        $data = json_decode($request->getContent(), true);
        $email = $data['email'] ?? '';
        $password = $data['password'] ?? '';

        // Find user by email
        $user = $entityManager->getRepository(User::class)->findOneBy(['email' => $email]);

        if (!$user || !$passwordHasher->isPasswordValid($user, $password)) {
            return new JsonResponse(['message' => 'Invalid credentials'], 400);
        }

        // Generate API token
        $token = bin2hex(random_bytes(32));
        $user->setApiToken($token);

        // Save the token in the database
        $entityManager->persist($user);
        $entityManager->flush();

        // Return the user's role along with the token and other info
        return new JsonResponse([
            'message' => 'Login successful',
            'role' => $user->getRoles(),
            'apiToken' => $token,
        ]);
    }

    #[Route('/api/login', name: 'app_api_login', methods: ['POST'])]
    public function apiLogin(#[CurrentUser] ?User $user, EntityManagerInterface $entityManager): JsonResponse
    {
        if (null === $user) {
            return $this->json([
                'message' => 'Missing credentials',
            ], 401);
        }

        // Generate new API token
        $token = bin2hex(random_bytes(32));
        $user->setApiToken($token);

        // Save the token in the database
        $entityManager->persist($user);
        $entityManager->flush();

        return $this->json([
            'user' => $user->getUserIdentifier(),
            'token' => $token,
        ]);
    }
}
