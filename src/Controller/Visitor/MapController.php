<?php

namespace App\Controller\Visitor;

use App\Repository\ProjectRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class MapController extends AbstractController
{
    public function __construct(
        private ProjectRepository $projectRepository
    )
    {
        
    }


    #[Route('/map', name: 'app_map')]
    public function index(): Response
    {
        return $this->render('pages/visitor/map/index.html.twig', [
            'projects' => $this->projectRepository->findAll(),
        ]);
    }
}
