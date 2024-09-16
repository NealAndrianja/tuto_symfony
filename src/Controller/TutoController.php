<?php

namespace App\Controller;

use App\Entity\Tuto;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

class TutoController extends AbstractController
{
    #[Route('/tuto', name: 'app_tuto')]
    public function fetchAll(EntityManagerInterface $entityManager): JsonResponse
    {
        $tutos = $entityManager->getRepository(Tuto::class)->findAll();
        if(!$tutos){
            throw $this->createNotFoundException('Tutos not found');
        }
        $data = [];
        foreach ($tutos as $tuto) {
            $data[] = [
                'id' => $tuto->getId(),
                'name' => $tuto->getName(),
                'subtitle' => $tuto->getSubtitle(),
                'slug' => $tuto->getSlug(),
                'description' => $tuto->getDescription(),
                'image' => $tuto->getImage(),
                'video' => $tuto->getVideo(),
                'link' => $tuto->getLink(),
            ];
        }
    
        return new JsonResponse($data);
    }

    #[Route('/tuto/add', name: 'create_tuto')]
    public function createTuto(EntityManagerInterface $entityManager): JsonResponse
    {
        $tuto = new Tuto();
        $tuto->setName("laravel tutorial");
        $tuto->setSubtitle("Laravel tutorial");
        $tuto->setSlug("lrvl");
        $tuto->setDescription("This is a new tutorial about Laravel");
        $tuto->setImage("laravel.png");
        $tuto->setVideo("laravel.mp4");
        $tuto->setLink("http://videoAboutLaravel.link");

        $entityManager->persist($tuto);
        $entityManager->flush();

        $data = [
            'id' => $tuto->getId(),
            'name' => $tuto->getName(),
            'subtitle' => $tuto->getSubtitle(),
            'slug' => $tuto->getSlug(),
            'description' => $tuto->getDescription(),
            'image' => $tuto->getImage(),
            'video' => $tuto->getVideo(),
            'link' => $tuto->getLink(),
        ];

        return new JsonResponse($data);
    }
}
