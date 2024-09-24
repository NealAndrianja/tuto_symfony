<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class SigninController extends AbstractController
{
    #[Route('/signin', name: 'app_signin')]
    public function register(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher, ValidatorInterface $validator): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        if(!$data){
            return new JsonResponse(['message' => 'Invalid credentials'], 400);
        }

        //new user
        $user = new User();
        $user->setEmail($data['email'] ?? '');
        $user->setPassword($data['password'] ?? '');

        // Determine role: simple user by default, admin only if explicitly set
        $role = $data['role'] ?? 'ROLE_USER';
        
        if (!in_array($role, ['ROLE_USER', 'ROLE_ADMIN'])) {
            return new JsonResponse(['message' => 'Invalid role specified'], 400);
        }

        // Set the role
        $user->setRoles([$role]);


        //Validation
        $errors = $validator->validate($user);
        if (count($errors) > 0) {
            $errorMessages = [];
            foreach ($errors as $err) {
                $errorMessages[] = $err->getMessage();
            }
            return new JsonResponse($errorMessages, 400);
        }

        //hash password
        $hashedPassword = $passwordHasher->hashPassword($user, $data['password']);
        $user->setPassword($hashedPassword);

        //persist user data
        $entityManager->persist($user);
        $entityManager->flush();

        //return success response
        return new JsonResponse(['message' => 'User created successfully'], 201);
    }
}
