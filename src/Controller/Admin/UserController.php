<?php

namespace App\Controller\Admin;

use App\Entity\User;
use DateTimeImmutable;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/admin')]
class UserController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private UserRepository $userRepository
    ) {
    }

    #[Route('/user/list', name: 'admin_user_index')]
    public function index(): Response
    {
        
        return $this->render('pages/admin/user/index.html.twig', [
            "users" => $this->userRepository->findAll()
        ]);
    }

    #[Route('/user/{id<\d+>}/delete', name: 'admin_user_delete')]
    public function delete(User $user, Request $request): Response
    {
        if ($this->isCsrfTokenValid('delete_user_' . $user->getId(), $request->request->get('_csrf_token'))) {
            // Si le token est valide

            $this->addFlash("danger", "L'utilisateur a été supprimer");

            // On fait appel a entityManager pour préparer la requete
            $this->entityManager->remove($user);

            // On fait appel a entityManager pour exécuter la requete
            $this->entityManager->flush();
        }

        return $this->redirectToRoute("admin_user_index");
    }

    #[Route('/user/{id<\d+>}/banned', name: 'admin_user_banned', methods: ["POST"])]
    public function banned(User $user, Request $request): Response
    {
        // Si le csrf token est valide
        if ($this->isCsrfTokenValid('banned_user_' . $user->getId(), $request->request->get('_csrf_token'))) {

            // On verifie si l'utilisateur possède un rôle admin
            $checkRoleAdmin = false;
            foreach ( $user->getRoles() as $key => $value) {
                if ( $value == "ROLE_SUPER_ADMIN" )
                {
                    $checkRoleAdmin = true;
                }
                if ( $value == "ROLE_ADMIN" )
                {
                    $checkRoleAdmin = true;
                }
            }

            if ( $checkRoleAdmin )
            {
                // Message flash
                $this->addFlash("warning", "Un utilisateur avec un rôle administrateur ne peu pas être banni.");
            } 
            else
            {

                // Si l'user n'a pas encore été banni
                if (false === $user->isBanned()) {
                    // On ban l'utilisateur
                    $user->setBanned(true);

                    // Mise à jour de la date de bannisement
                    $user->setUpdatedAt(new DateTimeImmutable());

                    // Mise à jour de la date de publication
                    $user->setBannedAt(new DateTimeImmutable());

                    // Utilisation de l'entity manager pour préparer la requete
                    $this->entityManager->persist($user);

                    // Message flash
                    $this->addFlash("success", "L'utilisateur a été banni.");
                } else {

                    // On retire la publication de l'article
                    $user->setBanned(false);

                    // Mise à jour de la date de modification
                    $user->setUpdatedAt(new DateTimeImmutable());

                    // Mise à jour de la date de publication
                    $user->setBannedAt(null);

                    // Utilisation de l'entity manager pour préparer la requete
                    $this->entityManager->persist($user);

                    // Message flash
                    $this->addFlash("success", "L'utilisateur a été retiré de la liste des bannissements.");
                }

                // On utilise l'entity manager pour exécuter la requête préparer
                $this->entityManager->flush($user);
            }
        }

        // Redirection et on arrete l'execution du script avec return
        return $this->redirectToRoute("admin_user_index");
    }

    #[Route('/user/{id<\d+>}/modo', name: 'admin_user_modo', methods: ["POST"])]
    public function modo(User $user, Request $request): Response
    {
        if ($this->isCsrfTokenValid('modo_user_' . $user->getId(), $request->request->get('_csrf_token'))) {
            // Si le token est valide

            if ( $user->getRoles()[0] == 'ROLE_MODERATOR' )
            {
                $user->setRoles([]);

                // Message flash
                $this->addFlash("success", "Le modérateur a été rétrogradé au rôle d'utilisateur ordinaire.");
            } else 
            {
                $user->setRoles(['ROLE_MODERATOR']);

                // Message flash
                $this->addFlash("success", "L'utilisateur a été promu modérateur.");
            }

            $this->entityManager->persist($user);
            // On utilise l'entity manager pour exécuter la requête préparer
            $this->entityManager->flush($user);

        }

        return $this->redirectToRoute("admin_user_index");

    }

}
