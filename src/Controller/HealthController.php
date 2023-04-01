<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HealthController extends AbstractController
{
    #[Route(path: '/health')]
    public function health(): Response
    {
        return new Response("Api is running", Response::HTTP_OK);
    }
}
