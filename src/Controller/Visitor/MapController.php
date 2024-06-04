<?php

namespace App\Controller\Visitor;

use App\Repository\ProjectRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class MapController extends AbstractController
{
    public function __construct(
        private ProjectRepository $projectRepository
    ) {
    }

    #[Route('/map', name: 'app_map')]
    public function index(SerializerInterface $serializer): Response
    {
        $projects = $this->projectRepository->findAll();
        return $this->render('pages/visitor/map/index.html.twig', [
            'markers' => $serializer->serialize($projects, 'json', ['groups' => ['project_read']]), // On passe les données sérialisé a la vue
            'projects' => $projects // On passe les données sans sérialisation
        ]);
    }
}
