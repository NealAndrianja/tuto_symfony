<?php

namespace App\Controller;

use App\Entity\ApiToken;
use App\Entity\DeliveryMode;
use App\Entity\SmallPackage;
use App\Entity\User;
use App\Enums\PackageStatus;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class SmallPackageController extends AbstractController
{

    private function getUserFromToken(Request $request, EntityManagerInterface $entityManager): ?User
    {
        $tokenString = $request->headers->get('Authorization');
        
        if ($tokenString) {
            // Remove 'Bearer ' prefix if present
            $tokenString = str_replace('Bearer ', '', $tokenString);
            
            $apiToken = $entityManager->getRepository(ApiToken::class)->findOneBy(['tokenString' => $tokenString]);
            if ($apiToken && $apiToken->getUser()) {
                return $apiToken->getUser();
            }
        }

        return null;
    }

    private function formatSmallPackageResponse(array $smallPackages): JsonResponse
    {
        $responseData = [];
        foreach ($smallPackages as $package) {
            $responseData[] = [
                'id' => $package->getId(),
                'tracking_code' => $package->getTrackingCode(),
                'reception_date' => $package->getReceptionDate()->format('Y-m-d H:i:s'),
                'customer_code' => $package->getCustomer() ? $package->getCustomer()->getCustomerCode() : null,
                'status' => $package->getStatus(),
                'weight' => $package->getWeight(),
                'dimensions' => $package->getDimensions(),
                'volume' => $package->getVolume(),
                'delivery_mode' => $package->getDeliveryMode() ? $package->getDeliveryMode()->getMode()->value : null,
            ];
        }

        return new JsonResponse($responseData, 200);
    }

    #[Route('/small/package/add', name: 'app_small_package')]
    public function createSmallPackage(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        if (!$data) {
            return new JsonResponse(['message' => 'Invalid data'], 400);
        }

        // Create a new small package entity and persist it
        $smallPackage = new SmallPackage();
        $smallPackage->setTrackingCode($data['tracking_code'] ?? null);

        $smallPackage->setStatus(PackageStatus::ARRIVE_CN->value);
        $smallPackage->setReceptionDate(new DateTimeImmutable());

        // Set customer if customer_code is provided
        if (!empty($data['customer_code'])) {
            $customer = $entityManager->getRepository(User::class)->findOneBy(['customerCode' => $data['customer_code']]);
            if ($customer) {
                $smallPackage->setCustomer($customer);
            } else {
                return new JsonResponse(['message' => 'Customer not found'], 404);
            }
        }

        // Set optional fields if available
        if (!empty($data['weight'])) {
            $smallPackage->setWeight($data['weight']);
        }

        if (!empty($data['dimensions']) && is_array($data['dimensions']) && count($data['dimensions']) === 3) {
            [$length, $width, $height] = $data['dimensions'];
            $smallPackage->setDimensions($data['dimensions']);
            $volume = $length * $width * $height;
            $smallPackage->setVolume($volume);
        }

        if (!empty($data['delivery_mode_id'])) {
            $deliveryMode = $entityManager->getRepository(DeliveryMode::class)->find($data['delivery_mode_id']);
            if ($deliveryMode) {
                $smallPackage->setDeliveryMode($deliveryMode);
            }
        }

        $entityManager->persist($smallPackage);
        $entityManager->flush();

        return new JsonResponse([
            'message' => 'Small package created successfully',
            'package' => [
                'id' => $smallPackage->getId(),
                'tracking_code' => $smallPackage->getTrackingCode(),
                'customer_code' => $smallPackage->getCustomer()->getCustomerCode(),
                'status' => $smallPackage->getStatus(),
                'reception_date' => $smallPackage->getReceptionDate()->format('Y-m-d H:i:s'),
                'deliveryMode' => $smallPackage->getDeliveryMode()->getMode(),
                'weight' => $smallPackage->getWeight(),
                'dimensions' => $smallPackage->getDimensions(),
                'volume' => $smallPackage->getVolume(),
            ],
        ], 201);
    }

    #[Route('/small/packages', name: 'get_all_small_packages', methods: ['GET'])]
    public function getAll(EntityManagerInterface $entityManager): JsonResponse
    {
        $smallPackages = $entityManager->getRepository(SmallPackage::class)->findAll();

        $responseData = $this->formatSmallPackageResponse($smallPackages);
     

        return $responseData;
    }

    #[Route('/small/packages/user', name: 'user_small_packages', methods: ['GET'])]
    public function getUserSmallPackages(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        // Get the user from the token in the request header
        $user = $this->getUserFromToken($request, $entityManager);

        if (!$user) {
            return new JsonResponse(['message' => 'Unauthorized'], 401);
        }


        // If the user is not an admin, get only their small packages
        if (in_array('ROLE_USER', $user->getRoles())) {
            $smallPackages = $entityManager->getRepository(SmallPackage::class)->findBy(['customer' => $user]);

            return $this->formatSmallPackageResponse($smallPackages);
        }

        return new JsonResponse(['message' => 'Access denied'], 403);
    }
}
