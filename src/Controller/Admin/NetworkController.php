<?php

namespace App\Controller\Admin;

use DateTimeImmutable;
use App\Entity\Network;
use App\Form\NetworkFormType;
use App\Repository\NetworkRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/admin')]
class NetworkController extends AbstractController
{

    public function __construct(
        private EntityManagerInterface $entityManager,
        private NetworkRepository $networkRepository,
    ) {
    }

    #[Route('/network/list', name: 'admin_network_index', methods: ["GET"])]
    public function index(): Response
    {
        return $this->render('pages/admin/network/index.html.twig', [
            "networks" => $this->networkRepository->findAll()
        ]);
    }

    #[Route('/network/create', name: 'admin_network_create', methods: ["GET", "POST"])]
    public function create(Request $request): Response
    {

        $network = new Network();

        $form = $this->createForm(NetworkFormType::class, $network);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $network->setCreatedAt(new DateTimeImmutable());
            $network->setUpdatedAt(new DateTimeImmutable());

            $this->entityManager->persist($network);
            $this->entityManager->flush();

            $this->addFlash("success", "Le réseau a été ajouter avec succès");

            return $this->redirectToRoute('admin_network_index');
        }

        return $this->render('pages/admin/network/create.html.twig', [
            "form" => $form->createView()
        ]);
    }

    #[Route('/network/{id<\d+>}/edit', name: 'admin_network_edit', methods: ["GET", "POST"])]
    public function edit(Network $network, Request $request): Response
    {

        $form = $this->createForm(NetworkFormType::class, $network);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $network->setUpdatedAt(new DateTimeImmutable());

            $this->entityManager->persist($network);
            $this->entityManager->flush();

            $this->addFlash("success", "Le réseau a été modifier avec succès");

            return $this->redirectToRoute('admin_network_index');
        }

        return $this->render('pages/admin/network/edit.html.twig', [
            "form" => $form->createView(),
            "network" => $network
        ]);
    }

    #[Route('/network/{id<\d+>}/delete', name: 'admin_network_delete', methods: ["POST"])]
    public function delete(Network $network, Request $request): Response
    {

        if ($this->isCsrfTokenValid('delete_network_' . $network->getId(), $request->request->get('_csrf_token'))) {
            // Si le token est valide

            $this->addFlash("danger", "La réseau a été supprimer");

            // On fait appel a entityManager pour préparer la requete
            $this->entityManager->remove($network);

            // On fait appel a entityManager pour exécuter la requete
            $this->entityManager->flush();
        }

        return $this->redirectToRoute("admin_network_index");
    }
}
