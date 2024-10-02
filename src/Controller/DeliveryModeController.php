<?php

namespace App\Controller;

use App\Entity\DeliveryMode;
use App\Enums\DeliveryModeType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class DeliveryModeController extends AbstractController
{
    #[Route('/delivery/modes', name: 'app_delivery_mode')]
    public function getAll(EntityManagerInterface $entityManager): JsonResponse
    {
        $deliveryModes = $entityManager->getRepository(DeliveryMode::class)->findAll();
        if (!$deliveryModes) {
            throw $this->createNotFoundException('Delivery modes not found');
        }
        $data = [];
        foreach ($deliveryModes as $deliveryMode) {
            $data[] = [
                'id' => $deliveryMode->getId(),
                'mode' => $deliveryMode->getMode(),
                'fee' => $deliveryMode->getFee(),
            ];
        }
        return new JsonResponse($data);
    }

    #[Route('/delivery/modes/{id}', name: 'find_delivery_mode', methods: ['GET'])]
    public function getOne(EntityManagerInterface $entityManager, int $id): JsonResponse
    {
        $deliveryMode = $entityManager->getRepository(DeliveryMode::class)->find($id);
        if (!$deliveryMode) {
            throw $this->createNotFoundException('Delivery mode not found');
        }
        return new JsonResponse([
            'id' => $deliveryMode->getId(),
            'mode' => $deliveryMode->getMode(),
            'fee' => $deliveryMode->getFee(),
        ]);
    }

    #[Route('/delivery/modes/add', name: 'add_delivery_mode')]
    public function createDeliveryMode(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        if (!$data) {
            return new JsonResponse(['message' => 'Invalid data'], 400);
        }

        // Convert the string to the appropriate enum instance
        try {
            $mode = isset($data['mode']) ? DeliveryModeType::from($data['mode']) : null;
        } catch (\ValueError $e) {
            return new JsonResponse(['message' => 'Invalid delivery mode'], 400);
        }

        // Create new delivery mode
        $deliveryMode = new DeliveryMode();
        $deliveryMode->setMode($mode);
        $deliveryMode->setFee($data['fee'] ?? 0);

        $entityManager->persist($deliveryMode);
        $entityManager->flush();

        return new JsonResponse(['message' => 'Delivery mode created successfully', 'deliveryMode' => [
            'id' => $deliveryMode->getId(),
            'mode' => $deliveryMode->getMode()->value,
            'fee' => $deliveryMode->getFee(),
        ]], 201);
    }

    #[Route('/delivery/modes/{id}', name: 'update_delivery_mode', methods: ['PUT'])]
    public function updateDeliveryMode(int $id, Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $deliveryMode = $entityManager->getRepository(DeliveryMode::class)->find($id);
        if (!$deliveryMode) {
            return new JsonResponse(['message' => 'Delivery mode not found'], 404);
        }

        $data = json_decode($request->getContent(), true);
        if (!$data) {
            return new JsonResponse(['message' => 'Invalid data'], 400);
        }
        // Update the fields only if they are present in the request data
        if (isset($data['mode'])) {
            try {
                $deliveryMode->setMode(DeliveryModeType::from($data['mode']));
            } catch (\ValueError $e) {
                return new JsonResponse(['message' => 'Invalid delivery mode'], 400);
            }
        }

        if (isset($data['fee'])) {
            $deliveryMode->setFee($data['fee']);
        }

        // Save changes to the database
        $entityManager->persist($deliveryMode);
        $entityManager->flush();

        return new JsonResponse(['message' => 'Delivery mode updated successfully', 'deliveryMode' => [
            'id' => $deliveryMode->getId(),
            'mode' => $deliveryMode->getMode()?->value,
            'fee' => $deliveryMode->getFee(),
        ]], 200);
    }

    #[Route('/delivery/modes/{id}', name: 'delete_delivery_mode', methods: ['DELETE'])]
    public function deleteDeliveryMode(int $id, EntityManagerInterface $entityManager): JsonResponse
    {
        $deliveryMode = $entityManager->getRepository(DeliveryMode::class)->find($id);
        if (!$deliveryMode) {
            return new JsonResponse(['message' => 'Delivery mode not found'], 404);
        }
        // Remove the delivery mode from the database
        $entityManager->remove($deliveryMode);
        $entityManager->flush();

        return new JsonResponse(['message' => 'Delivery mode deleted successfully'], 204);
    }
}
