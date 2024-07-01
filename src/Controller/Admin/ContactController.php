<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Entity\Contact;
use App\Repository\UserRepository;
use App\Repository\ContactRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/modo')]
class ContactController extends AbstractController
{

    public function __construct(
        private EntityManagerInterface $entityManager,
        private ContactRepository $contactRepository,
        private UserRepository $userRepository
    ) {
    }

    #[Route('/contact/list', name: 'admin_contact_index')]
    public function index(): Response
    {
        /**
         * Récupérons l'utilisateur connecté
         * 
         * @var User
         */

        // On récupere les données de l'utilisateur connecté
        $user = $this->getUser();

        return $this->render('pages/admin/contact/index.html.twig', [
            "contacts" => $this->contactRepository->findAll(),
            "users" => $this->userRepository->findAll()
        ]);
    }


    #[Route('/contact/{id<\d+>}/delete', name: 'admin_contact_delete', methods: ["POST"])]
    public function delete(Contact $contact, Request $request): Response
    {

        if ($this->isCsrfTokenValid('delete_contact_' . $contact->getId(), $request->request->get('_csrf_token'))) {
            // Si le token est valide

            $this->addFlash("danger", "Le message a été supprimer");

            // On fait appel a entityManager pour préparer la requete
            $this->entityManager->remove($contact);

            // On fait appel a entityManager pour exécuter la requete
            $this->entityManager->flush();
        }

        return $this->redirectToRoute("admin_contact_index");
    }
}
