<?php

namespace App\Controller\Admin;

use App\Entity\Tag;
use DateTimeImmutable;
use App\Form\TagFormType;
use App\Repository\TagRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/admin')]
class TagController extends AbstractController
{

    public function __construct(
        private EntityManagerInterface $entityManager,
        private TagRepository $tagRepository
    ) {
    }

    #[Route('/tag/list', name: 'admin_tag_index')]
    public function index(): Response
    {
        return $this->render('pages/admin/tag/index.html.twig', [
            "tags" => $this->tagRepository->findAll()
        ]);
    }

    #[Route('/tag/create', name: 'admin_tag_create')]
    public function create(Request $request): Response
    {

        $tag = new Tag();

        $form = $this->createForm(TagFormType::class, $tag);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $tag->setCreatedAt(new DateTimeImmutable());
            $tag->setUpdatedAt(new DateTimeImmutable());

            $this->entityManager->persist($tag);
            $this->entityManager->flush();

            $this->addFlash("success", "Le tag a été ajouter avec succès");

            return $this->redirectToRoute('admin_tag_index');
        }

        return $this->render('pages/admin/tag/create.html.twig', [
            "formTag" => $form->createView()
        ]);
    }

    #[Route('/tag/{id<\d+>}/edit', name: 'admin_tag_edit')]
    public function edit(Tag $tag, Request $request): Response
    {

        $form = $this->createForm(TagFormType::class, $tag);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $tag->setUpdatedAt(new DateTimeImmutable());

            $this->entityManager->persist($tag);
            $this->entityManager->flush();

            $this->addFlash("success", "Le tag a été modifier avec succès");

            return $this->redirectToRoute('admin_tag_index');
        }

        return $this->render('pages/admin/tag/edit.html.twig', [
            "formTag" => $form->createView()
        ]);
    }

    #[Route('/tag/{id<\d+>}/delete', name: 'admin_tag_delete', methods: ["POST"])]
    public function delete(Tag $tag, Request $request): Response
    {
        if ($this->isCsrfTokenValid('delete_tag_' . $tag->getId(), $request->request->get('_csrf_token'))) {
            // Si le token est valide

            $this->addFlash("danger", "Le tag {$tag->getName()} a été supprimer");

            // On fait appel a entityManager pour préparer la requete
            $this->entityManager->remove($tag);

            // On fait appel a entityManager pour exécuter la requete
            $this->entityManager->flush();
        }

        return $this->redirectToRoute("admin_tag_index");
    }
}
