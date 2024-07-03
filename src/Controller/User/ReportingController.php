<?php

namespace App\Controller\User;

use App\Entity\User;
use DateTimeImmutable;
use App\Entity\Comment;
use App\Entity\Project;
use App\Entity\Reporting;
use App\Form\UserReportingFormType;
use App\Repository\NetworkRepository;
use App\Repository\ReportingRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/user')]
class ReportingController extends AbstractController
{

    public function __construct(
        private EntityManagerInterface $entityManager,
        private ReportingRepository $reportingRepository,
        private NetworkRepository $networkRepository,
    ) {
    }

    #[Route('/reporting/project/{id<\d+>}/create', name: 'user_reporting_project_create')]
    public function createProject(Project $project, Request $request): Response
    {

        /**
        * Récupérons l'utilisateur connecté
        * 
        * @var User
        */

        // On récupère l'utilisateur connecté et on l'ajoute a l'objet
        $user = $this->getUser();

        if ( count($this->reportingRepository->findBy(array("user" => $user,"project" => $project->getId()))) > 0 )
        {
            $this->addFlash("warning", "Vous avez déjà signalé le projet.");
            return $this->redirectToRoute('user_project_show', ['id' => $project->getId(),"networks" => $this->networkRepository->findAll()]);
        }

        // On instancie un nouvelle objet reporting
        $reporting = new Reporting();

        // Création du formulaire
        $form = $this->createForm(UserReportingFormType::class, $reporting);

        // On récupere les donnés renvoyer après avoir rempli le formulaire
        $form->handleRequest($request);

        // On test la validation du formulaire
        if ($form->isSubmitted() && $form->isValid()) {

            // On ajoute la date de création et de modification
            $reporting->setCreatedAt(new DateTimeImmutable());

            $reporting->setUser($user);

            // On récupère le project et on l'ajoute a l'objet
            $reporting->setProject($project);

            // On ajoute l'objet en BDD
            $this->entityManager->persist($reporting);
            $this->entityManager->flush();

            $this->addFlash("success", "Le projet a été signaler avec succès.");

            return $this->redirectToRoute('user_project_show', ['id' => $reporting->getProject()->getId(),"networks" => $this->networkRepository->findAll()]);
        }

        return $this->render('pages/user/reporting/project/create.html.twig', [
            'form' => $form,
            "networks" => $this->networkRepository->findAll(),

        ]);
    }

    #[Route('/reporting/comment/{id<\d+>}/create', name: 'user_reporting_comment_create')]
    public function createComment(Comment $comment, Request $request): Response
    {

        /**
        * Récupérons l'utilisateur connecté
        * 
        * @var User
        */

        // On récupère l'utilisateur connecté et on l'ajoute a l'objet
        $user = $this->getUser();

        if ( count($this->reportingRepository->findBy(array("user" => $user, "comment" => $comment->getId()))) > 0 )
        {
            $this->addFlash("warning", "Vous avez déjà signalé le commentaire.");
            return $this->redirectToRoute('user_project_show', ['id' => $comment->getProject()->getId(), "networks" => $this->networkRepository->findAll(),
        ]);
        }

        // On instancie un nouvelle objet reporting
        $reporting = new Reporting();

        // Création du formulaire
        $form = $this->createForm(UserReportingFormType::class, $reporting);

        // On récupere les donnés renvoyer après avoir rempli le formulaire
        $form->handleRequest($request);

        // On test la validation du formulaire
        if ($form->isSubmitted() && $form->isValid()) {

            // On ajoute la date de création et de modification
            $reporting->setCreatedAt(new DateTimeImmutable());

            /**
            * Récupérons l'utilisateur connecté
            * 
            * @var User
            */

            // On récupère l'utilisateur connecté et on l'ajoute a l'objet
            $user = $this->getUser();
            $reporting->setUser($user);

            // On récupère le commentaire et on l'ajoute a l'objet
            $reporting->setComment($comment);

            // On ajoute l'objet en BDD
            $this->entityManager->persist($reporting);
            $this->entityManager->flush();

            $this->addFlash("success", "Le commentaire a été signaler avec succès.");

            return $this->redirectToRoute('user_project_show', ['id' => $reporting->getComment()->getProject()->getId(), "networks" => $this->networkRepository->findAll()]);
        }

        return $this->render('pages/user/reporting/comment/create.html.twig', [
            'form' => $form,
            "networks" => $this->networkRepository->findAll(),

        ]);
    }
}
