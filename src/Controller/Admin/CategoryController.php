<?php

namespace App\Controller\Admin;

use DateTimeImmutable;
use App\Entity\Category;
use App\Form\CategoryFormType;
use App\Repository\CategoryRepository;
use App\Repository\ProjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use phpDocumentor\Reflection\Project;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/admin')]
class CategoryController extends AbstractController
{

    public function __construct(
        private EntityManagerInterface $entityManager,
        private CategoryRepository $categoryRepository,
        private ProjectRepository $projectRepository
    ) {
    }

    #[Route('/category/list', name: 'admin_category_index', methods: ["GET"])]
    public function index(): Response
    {
        return $this->render('pages/admin/category/index.html.twig', [
            "categories" => $this->categoryRepository->findAll()
        ]);
    }

    #[Route('/category/create', name: 'admin_category_create', methods: ["GET", "POST"])]
    public function create(Request $request): Response
    {

        $category = new Category();

        $form = $this->createForm(CategoryFormType::class, $category);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $category->setCreatedAt(new DateTimeImmutable());
            $category->setUpdatedAt(new DateTimeImmutable());

            $this->entityManager->persist($category);
            $this->entityManager->flush();

            $this->addFlash("success", "La catégorie a été ajouter avec succès");

            return $this->redirectToRoute('admin_category_index');
        }

        return $this->render('pages/admin/category/create.html.twig', [
            "formCategory" => $form->createView()
        ]);
    }

    #[Route('/category/{id<\d+>}/edit', name: 'admin_category_edit', methods: ["GET", "POST"])]
    public function edit(Category $category, Request $request): Response
    {

        $form = $this->createForm(CategoryFormType::class, $category);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $category->setUpdatedAt(new DateTimeImmutable());

            $this->entityManager->persist($category);
            $this->entityManager->flush();

            $this->addFlash("success", "La catégorie a été modifier avec succès");

            return $this->redirectToRoute('admin_category_index');
        }

        return $this->render('pages/admin/category/edit.html.twig', [
            "formCategory" => $form->createView(),
            "category" => $category
        ]);
    }

    #[Route('/category/{id<\d+>}/delete', name: 'admin_category_delete', methods: ["POST"])]
    public function delete(Category $category, Request $request): Response
    {
        $projects = $this->projectRepository->findAll();

        foreach ($projects as $project) {
            if ( $project->getCategory() == $category )
            {
                $this->addFlash("danger", "La catégorie {$category->getName()} ne peu pas être supprimer car au moins un projet l'utilise.");

                return $this->redirectToRoute("admin_category_index");
            }
        }

        if ($this->isCsrfTokenValid('delete_category_' . $category->getId(), $request->request->get('_csrf_token'))) {
            // Si le token est valide

            $this->addFlash("danger", "La catégorie {$category->getName()} a été supprimer");

            // On fait appel a entityManager pour préparer la requete
            $this->entityManager->remove($category);

            // On fait appel a entityManager pour exécuter la requete
            $this->entityManager->flush();
        }

        return $this->redirectToRoute("admin_category_index");
    }
}
