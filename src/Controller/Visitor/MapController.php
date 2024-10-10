<?php

namespace App\Controller\Visitor;

use App\Repository\CategoryRepository;
use DateTimeImmutable;
use App\Repository\NetworkRepository;
use App\Repository\ProjectRepository;
use App\Repository\TagRepository;
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
        private CategoryRepository $categoryRepository,
        private TagRepository $tagRepository
    ) {
    }

    #[Route('/map', name: 'app_map')]
    public function index(SerializerInterface $serializer, PaginatorInterface $paginator, Request $request): Response
    {
        $newProject = [];
        
        $projects = $this->projectRepository->findBy(["isPublished"=>1],array('startDate'=>'DESC'));

        // On récupére que les projets en cours ou a venir
        foreach ($projects as $project) {

            if ($project->getEndDate() != null) {
                if ($project->getEndDate() >= new DateTimeImmutable()) {
                    array_push($newProject, $project);
                }
            } else {
                array_push($newProject, $project);
            }
        }

        // Récupération des différents département des projets en cours ou a venir
        $arrayCode= [];

        foreach ($this->projectRepository->findBy(["isPublished"=>1]) as $project) {

            if ($project->getEndDate() != null)
            {
                if ($project->getEndDate() >= new DateTimeImmutable() )
                {
                    $code = (string)$project->getCode();
                    $code = $code[0].$code[1];
                    array_push($arrayCode, $code);
                }
            }
            else {
                $code = (string)$project->getCode();
                $code = $code[0].$code[1];
                array_push($arrayCode, $code);
            }

        }

        $listCode = array_unique($arrayCode);

        $pagination = $paginator->paginate(
            $newProject, /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            4 /*limit per page*/
        );
    
        return $this->render('pages/visitor/map/index.html.twig', [
            'markers' => $serializer->serialize($newProject, 'json', ['groups' => ['project_read']]), // On passe les données sérialisé a la vue
            'pagination' => $pagination, // On passe les données sans sérialisation
            "networks" => $this->networkRepository->findAll(),
            "categories" => $this->categoryRepository->findAll(),
            "listCode" => $listCode,
        ]);
    }

    #[Route('/map/filter', name: 'app_map_filter')]
    public function filter(SerializerInterface $serializer, PaginatorInterface $paginator, Request $request): Response
    {

        $newProject = [];
        
        // On récupére tout les projets publier dans l'ordre decroissant
        $projects = $this->projectRepository->findBy(["isPublished"=>1],array('startDate'=>'DESC'));

        // On récupére que les projets en cours ou a venir
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
            
        }

        // Filtre par catégorie si le formulaire renvoi autre chose que "null"
        if ( $request->request->get("category") !== "null"){

            $projectsUpdate = [];

            // On boucle sur les projets afin de récupéré que ce d'une certaine catégorie
            foreach ($newProject as $project) {
                if ( $project->getCategory()->getname() === $request->request->get("category")){
                    array_push($projectsUpdate, $project);
                }
            }

            $newProject = $projectsUpdate;

        }

        // Filtre par département si le formulaire renvoi autre chose que "null"
        if ( $request->request->get("code") !== "null"){

            $projectsUpdate = [];

            // On boucle sur les projets afin de récupéré que ce d'une certaine département
            foreach ($newProject as $project) {

                $code = (string)$project->getCode();
                $code = $code[0].$code[1];

                if ( $code === $request->request->get("code")){
                    array_push($projectsUpdate, $project);
                }
            }

            $newProject = $projectsUpdate;

        }

        // Récupération des différents département des projets en cours ou a venir
        $arrayCode= [];

        foreach ($this->projectRepository->findBy(["isPublished"=>1]) as $project) {

            if ($project->getEndDate() != null)
            {
                if ($project->getEndDate() >= new DateTimeImmutable() )
                {
                    $code = (string)$project->getCode();
                    $code = $code[0].$code[1];
                    array_push($arrayCode, $code);
                }
            }
            else {
                $code = (string)$project->getCode();
                $code = $code[0].$code[1];
                array_push($arrayCode, $code);
            }

        }

        $listCode = array_unique($arrayCode);

        $pagination = $paginator->paginate(
            $newProject, /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            4 /*limit per page*/
        );
    
        return $this->render('pages/visitor/map/index.html.twig', [
            'markers' => $serializer->serialize($newProject, 'json', ['groups' => ['project_read']]), // On passe les données sérialisé a la vue
            'pagination' => $pagination, // On passe les données sans sérialisation
            "networks" => $this->networkRepository->findAll(),
            "categories" => $this->categoryRepository->findAll(),
            "listCode" => $listCode,
        ]);
    }

    #[Route('/map/tag/{id<\d+>}', name: 'app_map_filter_tag')]
    public function filterTag($id, SerializerInterface $serializer, PaginatorInterface $paginator, Request $request): Response
    {
        $newProject = [];
        
        // On récupére tout les projets publier dans l'ordre decroissant
        $projects = $this->projectRepository->findBy(["isPublished"=>1],array('startDate'=>'DESC'));

        // On récupére que les projets en cours ou a venir
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
            
        }

        // On boucle sur les projets afin de récupéré que ce d'un certain tag
        $projectsUpdate = [];

        foreach ($newProject as $project) {
            foreach ($project->getTags() as $tag) {
                if ( $id == $tag->getId()){
                    array_push($projectsUpdate, $project);
                }
            }
        }
        
        // Récupération des différents département des projets en cours ou a venir
        $newProject = $projectsUpdate;
        $arrayCode= [];

        foreach ($this->projectRepository->findBy(["isPublished"=>1]) as $project) {

            if ($project->getEndDate() != null)
            {
                if ($project->getEndDate() >= new DateTimeImmutable() )
                {
                    $code = (string)$project->getCode();
                    $code = $code[0].$code[1];
                    array_push($arrayCode, $code);
                }
            }
            else {
                $code = (string)$project->getCode();
                $code = $code[0].$code[1];
                array_push($arrayCode, $code);
            }

        }

        $listCode = array_unique($arrayCode);

        $pagination = $paginator->paginate(
            $newProject, /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            4 /*limit per page*/
        );
    
        return $this->render('pages/visitor/map/index.html.twig', [
            'markers' => $serializer->serialize($newProject, 'json', ['groups' => ['project_read']]), // On passe les données sérialisé a la vue
            'pagination' => $pagination, // On passe les données sans sérialisation
            "networks" => $this->networkRepository->findAll(),
            "categories" => $this->categoryRepository->findAll(),
            "listCode" => $listCode,
        ]);
    }

}
