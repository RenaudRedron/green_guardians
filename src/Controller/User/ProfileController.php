<?php

namespace App\Controller\User;

use App\Entity\User;
use DateTimeImmutable;
use App\Form\ProfileFormType;
use App\Repository\ContactRepository;
use App\Repository\NetworkRepository;
use App\Form\UserResetPasswordFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

#[Route('/user')]
class ProfileController extends AbstractController
{

    public function __construct(
        private EntityManagerInterface $entityManager,
        private ContactRepository $contactRepository,
        private NetworkRepository $networkRepository,

    ) {
    }

    #[Route('/profile', name: 'user_profile')]
    public function index(): Response
    {   

        $user = $this->getUser();

        return $this->render('pages/user/profile/index.html.twig', [
            "user" => $user,
            "networks" => $this->networkRepository->findAll(),

        ]);
    }

    #[Route('/profile/edit', name: 'user_profile_edit')]
    public function edit(Request $request): Response
    {   
        /**
         * Récupérons l'utilisateur connecté
         * 
         * @var User
         */
        
        $user = $this->getUser();

        $form = $this->createForm(ProfileFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $user->setUpdatedAt(new DateTimeImmutable());

            $this->entityManager->persist($user);
            $this->entityManager->flush();

            $this->addFlash("success", "Le profil a été modifier avec succès");

            return $this->redirectToRoute('user_profile');
        }

        return $this->render('pages/user/profile/edit.html.twig', [
            "form" => $form,
            "networks" => $this->networkRepository->findAll(),

        ]);
    }
    
    #[Route('/profile/delete', name: 'user_profile_delete')]
    public function delete(Request $request, TokenStorageInterface $tokenStorage ): Response
    {
        /**
         * Récupérons l'utilisateur connecté
         * 
         * @var User
         */
        
        $user = $this->getUser();

        if ($this->isCsrfTokenValid('delete_user_' . $user->getId(), $request->request->get('_csrf_token'))) {
            // Si le token est valide

            // On séléctionne les données de la table contact qu'on souhaite passé avec un user sur null
            $contacts = $this->contactRepository->findBy(["user" => $user]);
            
            foreach ($contacts as $contact) {
                $contact->setUser(null);
                $this->entityManager->persist($contact);
            }
            $this->entityManager->flush();


            // On fait appel a entityManager pour préparer la requete
            $this->entityManager->remove($user);

            // On fait appel a entityManager pour exécuter la requete
            $this->entityManager->flush();

            // Vide la session
            $request->getSession()->invalidate();

            // Vide le token
            $tokenStorage->setToken(null); // TokenStorageInterface
        }

        return $this->redirectToRoute("app_logout");
    }

}
