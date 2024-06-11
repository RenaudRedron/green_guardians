<?php

namespace App\Controller\Admin;

use App\Repository\TagRepository;
use App\Repository\UserRepository;
use App\Repository\CommentRepository;
use App\Repository\ProjectRepository;
use App\Repository\CategoryRepository;
use App\Repository\ReportingRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/admin')]
class HomeController extends AbstractController
{

    public function __construct(
        private UserRepository $userRepository,
        private CategoryRepository $categoryRepository,
        private TagRepository $tagRepository,
        private ProjectRepository $projectRepository,
        private CommentRepository $commentRepository,
        private ReportingRepository $reportingRepository
    ) {
    }

    #[Route('/home', name: 'admin_home')]
    public function index(): Response
    {
        return $this->render('pages/admin/home/index.html.twig', [
            "users" => $this->userRepository->findAll(),
            "categories" => $this->categoryRepository->findAll(),
            "tags" => $this->tagRepository->findAll(),
            "projects" => $this->projectRepository->findAll(),
            "comments" => $this->commentRepository->findAll(),
            "reporting" => $this->reportingRepository->findAll(),
        ]);
    }
}
