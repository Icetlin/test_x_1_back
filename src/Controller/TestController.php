<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class TestController extends AbstractController
{
    /**
     * @Route("/api/test", name="api_test", methods={"GET"})
     */
    public function test(): \Symfony\Component\HttpFoundation\JsonResponse
    {
        // Здесь вы можете вернуть любые данные в формате JSON
        return $this->json(['message' => 'Привет из Symfony!']);
    }
}
