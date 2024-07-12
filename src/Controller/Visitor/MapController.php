<?php

namespace App\Controller\Visitor;

use DateTimeImmutable;
use App\Repository\NetworkRepository;
use App\Repository\ProjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class MapController extends AbstractController
{
    public function __construct(
        private ProjectRepository $projectRepository,
        private NetworkRepository $networkRepository,
    ) {
    }

    #[Route('/map', name: 'app_map')]
    public function index(SerializerInterface $serializer, PaginatorInterface $paginator, Request $request): Response
    {
        $newProject = [];
        $projects = $this->projectRepository->findBy(["isPublished"=>1],array('startDate'=>'DESC'));

        // On récupére que les projets qui nous pas dépassé leurs date de fin
        $i = 0;
        foreach ($projects as $project) {
            
            if ($project->getEndDate() != null)
            {
                if ($project->getEndDate() >= new DateTimeImmutable() )
                {
                    array_push($newProject, $project);
                }
            }
            else {
                array_push($newProject, $project);
            }
            
            $i++;
        }

        $pagination = $paginator->paginate(
            $newProject, /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            4 /*limit per page*/
        );
    
        return $this->render('pages/visitor/map/index.html.twig', [
            'markers' => $serializer->serialize($projects, 'json', ['groups' => ['project_read']]), // On passe les données sérialisé a la vue
            'pagination' => $pagination, // On passe les données sans sérialisation
            "networks" => $this->networkRepository->findAll(),
        ]);
    }
}
