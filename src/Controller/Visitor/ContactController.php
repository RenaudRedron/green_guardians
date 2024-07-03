<?php

namespace App\Controller\Visitor;

use DateTimeImmutable;
use App\Entity\Contact;
use App\Form\ContactFormType;
use App\Form\UserContactFormType;
use App\Repository\NetworkRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ContactController extends AbstractController
{

    public function __construct(
        private EntityManagerInterface $entityManager,
        private NetworkRepository $networkRepository,

    ) {
    }

    #[Route('/visitor/contact', name: 'app_contact')]
    public function index( Request $request ): Response
    {

        /**
         * Récupérons l'utilisateur connecté
         * 
         * @var User
         */

        // On récupere les données de l'utilisateur connecté
        $user = $this->getUser();

        // On instancie un nouvelle objet Contact
        $contact = new Contact();

        if ( $user ) {
            // Création du formulaire
            $contact->setFirstName($user->getFirstName());
            $contact->setLastName($user->getLastName());
            $contact->setEmail($user->getEmail());
            $contact->setPhone($user->getPhone());
            $user = $this->getUser();
            $contact->setUser($user);
            $form = $this->createForm(UserContactFormType::class, $contact);
        }
        else {
            // Création du formulaire
            $form = $this->createForm(ContactFormType::class, $contact);
        }

        // On récupere les donnés renvoyer après avoir rempli le formulaire
        $form->handleRequest($request);

        // On test la validation du formulaire
        if ($form->isSubmitted() && $form->isValid()) {
            // On ajoute la date de création
            $contact->setCreatedAt(new DateTimeImmutable());
            
            // On ajoute l'objet en BDD
            $this->entityManager->persist($contact);
            $this->entityManager->flush();

            $this->addFlash("success", "Le message a été envoyer avec succès.");

            return $this->redirectToRoute('app_contact');            
        }

        return $this->render('pages/visitor/contact/index.html.twig', [
            'form' => $form,
            "networks" => $this->networkRepository->findAll(),
        ]);
    }
}
