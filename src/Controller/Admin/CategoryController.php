<?php

namespace App\Controller\Admin;

use DateTimeImmutable;
use App\Entity\Category;
use App\Form\CategoryFromType;
use Doctrine\ORM\EntityManager;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/admin')]
class CategoryController extends AbstractController
{

    public function __construct( 
        private EntityManagerInterface $entityManager,
        private CategoryRepository $categoryRepository
    )
    {}

    #[Route('/category', name: 'admin_category_index', methods:["GET"])]
    public function index(): Response
    {
        return $this->render('pages/admin/category/index.html.twig', [
            "categories" => $this->categoryRepository->findAll()
        ]);
    }

    #[Route('/create', name: 'admin_category_create', methods:["GET","POST"])]
    public function create(Request $request): Response
    {

        $category = new Category();

        $form = $this->createForm(CategoryFromType::class, $category);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $category->setCreatedAt(new DateTimeImmutable());
            $category->setUpdatedAt(new DateTimeImmutable());

            $this->entityManager->persist($category);
            $this->entityManager->flush();

            $this->addFlash("success", "La catégorie a été ajoutée avec succès");

            return $this->redirectToRoute('admin_category_index');
        }

        return $this->render('pages/admin/category/create.html.twig', [
            "formCategory" => $form->createView()
        ]);
    }

    #[Route('/{id<\d+>}/edit', name: 'admin_category_edit', methods:["GET","POST"])]
    public function edit(Category $category, Request $request): Response
    {

        $form = $this->createForm(CategoryFromType::class, $category);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $category->setUpdatedAt(new DateTimeImmutable());

            $this->entityManager->persist($category);
            $this->entityManager->flush();

            $this->addFlash("success", "La catégorie a été modifiée avec succès");

            return $this->redirectToRoute('admin_category_index');
        }

        return $this->render('pages/admin/category/edit.html.twig', [
            "formCategory" => $form->createView()
        ]);
    }

    #[Route('/category/{id<\d+>}/delete', name: 'admin_category_delete', methods: ["POST"])]
    public function delete(Category $category, Request $request): Response
    {  
        if ( $this->isCsrfTokenValid('delete_category_'.$category->getId(), $request->request->get('_csrf_token') ) )
        {
            // Si le token est valide

            $this->addFlash("danger", "La catégorie {$category->getName()} a été supprimée");

            // On fait appel a entityManager pour préparer la requete
            $this->entityManager->remove($category);

            // On fait appel a entityManager pour exécuter la requete
            $this->entityManager->flush();

        }

        return $this->redirectToRoute("admin_category_index");
    }
}
