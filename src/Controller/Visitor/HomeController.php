<?php

namespace App\Controller\Visitor;

use App\Entity\Category;
use App\Repository\UserRepository;
use App\Repository\NetworkRepository;
use App\Repository\ProjectRepository;
use App\Repository\CategoryRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    public function __construct(
        private CategoryRepository $categoryRepository,
        private ProjectRepository $projectRepository,
        private NetworkRepository $networkRepository,
    ) {
    }

    #[Route('/', name: 'app_home')]
    public function index(): Response
    {
    
        $projects = $this->projectRepository->findAll();
        $categories = $this->categoryRepository->findAll();
        $networks = $this->networkRepository->findAll();

        return $this->render('pages/visitor/home/index.html.twig',[
            "categories" => $categories,
            "projects" => $projects,
            "networks" => $networks,
        ]);
    }
}
