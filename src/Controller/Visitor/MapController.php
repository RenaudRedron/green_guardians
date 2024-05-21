<?php

namespace App\Controller\Visitor;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class MapController extends AbstractController
{
    #[Route('/map', name: 'app_map')]
    public function index(): Response
    {
        return $this->render('pages/visitor/map/index.html.twig', [
            'controller_name' => 'MapController',
        ]);
    }
}