<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class DefaultController extends AbstractController {

    #[Route('/', name: 'app_index', methods: ['GET'])]
    public function index(): Response {

        return $this->render('index.html.twig', [
        ]);
    }

}
