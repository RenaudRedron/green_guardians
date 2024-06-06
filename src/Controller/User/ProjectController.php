<?php

namespace App\Controller\User;

use App\Entity\User;
use DateTimeImmutable;
use App\Entity\Comment;
use App\Entity\Project;
use App\Entity\ProjectUser;
use App\Form\CommentFormType;
use App\Form\UserProjectFormType;
use App\Repository\CommentRepository;
use App\Repository\ProjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\ProjectUserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ProjectController extends AbstractController
{

    public function __construct(
        private EntityManagerInterface $entityManager,
        private ProjectRepository $projectRepository,
        private ProjectUserRepository $projectUserRepository,
        private CommentRepository $commentRepository
    ) {
    }

    #[Route('/user/project/list', name: 'user_project_list', methods: ["GET"])]
    public function index(): Response
    {
        /**
         * Récupérons l'utilisateur connecté
         * 
         * @var User
         */

        // On récupere les données de l'utilisateur connecté
        $user = $this->getUser();

        return $this->render('pages/user/project/index.html.twig', [
            "projects" => $this->projectRepository->findBy(array("user" => $user->getId())), // On envoi les projets qui appartient a l'user connecté
            "userParticipates" => ( $user ? $this->projectUserRepository->findAll() : 0)
        ]);
    }

    #[Route('/user/project/create', name: 'user_project_create', methods: ["GET","POST"])]
    public function create(Request $request): Response
    {
        // On instancie un nouvelle objet Project
        $project = new Project();

        // Création du formulaire
        $form = $this->createForm(UserProjectFormType::class, $project);

        // On récupere les donnés renvoyer après avoir rempli le formulaire
        $form->handleRequest($request);

        // On test la validation du formulaire
        if ($form->isSubmitted() && $form->isValid()) {

            // Mise en forme de l'adresse pour l'envoi a l'api via curl
            $street = str_replace(" ", "+", $project->getStreet());
            $code = $project->getCode();
            $city = str_replace(" ", "+", $project->getCity());;

            // Envoi a l'api
            $curl = curl_init("http://api-adresse.data.gouv.fr/search/?q={$street}+{$code}+{$city}&type=housenumber");
            curl_setopt_array($curl, [
                CURLOPT_TIMEOUT => 1,
                CURLOPT_RETURNTRANSFER => true
            ]);

            // Récupération des information
            $data = curl_exec($curl);

            // Transformation du json en tableau
            $json = json_decode($data, true);

            // Si il existe aucune adresse avec les information entrés
            if ( empty($json["features"]) ){

                // Message d'erreur
                $this->addFlash("warning", "Cette adresse est introuvable");

            } else {

                if (count($json["features"]) == 0 ) {
                    // Message d'erreur
                    $this->addFlash("warning", "Cette adresse est introuvable");
                } else {
                    // On mais en forme l'adresse et on l'ajoute a objet
                    $address = $project->getStreet() . ' ' . $project->getCode() . ' ' . $project->getCity();                
                    $project->setAddress($address);

                    // On récupère la Latitude et on l'ajoute a objet
                    $lat = $json["features"][0]["geometry"]["coordinates"][1];
                    $project->setLatitude($lat);

                    // On récupère la Longitude et on l'ajoute a objet
                    $lng = $json["features"][0]["geometry"]["coordinates"][0];
                    $project->setLongitude($lng);

                    // On ajoute la date de création et de modification
                    $project->setCreatedAt(new DateTimeImmutable());
                    $project->setUpdatedAt(new DateTimeImmutable());

                    /**
                     * Récupérons l'utilisateur connecté
                     * 
                     * @var User
                     */

                    // On récupère l'utilisateur connecté et on l'ajoute a l'objet
                    $user = $this->getUser();
                    $project->setUser($user);

                    // Si la date de fin est plutôt que la date de début ou si la date de fin est avant la date actuelle
                    if ($project->getEndDate() != null && ($project->getStartDate() > $project->getEndDate() || (new DateTimeImmutable()) > $project->getEndDate())) {
                        $this->addFlash("warning", "La date de fin choisi pour le projet est incorrect.");
                    } else {

                        // On ajoute l'objet en BDD
                        $this->entityManager->persist($project);
                        $this->entityManager->flush();

                        $this->addFlash("success", "Le projet a été ajouter avec succès.");

                        return $this->redirectToRoute('user_project_list');
                    }
                } 

            }
            
        }

        return $this->render('pages/user/project/create.html.twig', [
            "form" => $form->createView()
        ]);
    }

    #[Route('/user/project/{id<\d+>}/edit', name: 'user_project_edit', methods: ["GET","POST"])]
    public function edit(Project $project,  Request $request): Response
    {

        // On controle si le projet que l'utilisateur souhaite modifier soit bien son projet
        if ($this->getUser() != $project->getUser()) {
            return $this->redirectToRoute('user_project_list');
        }

        // Création du formulaire
        $form = $this->createForm(UserProjectFormType::class, $project);

        // On récupere les donnés renvoyer après avoir rempli le formulaire
        $form->handleRequest($request);

        // On test la validation du formulaire
        if ($form->isSubmitted() && $form->isValid()) {

            // Mise en forme de l'adresse pour l'envoi a l'api via curl
            $street = str_replace(" ", "+", $project->getStreet());
            $code = $project->getCode();
            $city = str_replace(" ", "+", $project->getCity());;

            // Envoi a l'api
            $curl = curl_init("http://api-adresse.data.gouv.fr/search/?q={$street}+{$code}+{$city}&type=housenumber");
            curl_setopt_array($curl, [
                CURLOPT_TIMEOUT => 1,
                CURLOPT_RETURNTRANSFER => true
            ]);

            // Récupération des information
            $data = curl_exec($curl);

            // Transformation du json en tableau
            $json = json_decode($data, true);

            // Si il existe aucune adresse avec les information entrés
            if ( empty($json["features"]) ){

                // Message d'erreur
                $this->addFlash("warning", "Cette adresse est introuvable");

            } else {

                if (count($json["features"]) == 0 ) {
                    // Message d'erreur
                    $this->addFlash("warning", "Cette adresse est introuvable");
                } else {
                    // On mais en forme l'adresse et on l'ajoute a objet
                    $address = $project->getStreet() . ' ' . $project->getCode() . ' ' . $project->getCity();                
                    $project->setAddress($address);

                    // On récupère la Latitude et on l'ajoute a objet
                    $lat = $json["features"][0]["geometry"]["coordinates"][1];
                    $project->setLatitude($lat);

                    // On récupère la Longitude et on l'ajoute a objet
                    $lng = $json["features"][0]["geometry"]["coordinates"][0];
                    $project->setLongitude($lng);

                    // On ajoute la date de création et de modification
                    $project->setUpdatedAt(new DateTimeImmutable());

                    /**
                     * Récupérons l'utilisateur connecté
                     * 
                     * @var User
                     */

                    // On récupère l'utilisateur connecté et on l'ajoute a l'objet
                    $user = $this->getUser();
                    $project->setUser($user);

                    // Si la date de fin est plutôt que la date de début ou si la date de fin est avant la date actuelle
                    if ($project->getEndDate() != null && ($project->getStartDate() > $project->getEndDate() || (new DateTimeImmutable()) > $project->getEndDate())) {
                        $this->addFlash("warning", "La date de fin choisi pour le projet est incorrect.");
                    } else {

                        // On ajoute l'objet en BDD
                        $this->entityManager->persist($project);
                        $this->entityManager->flush();

                        $this->addFlash("success", "Le projet a été ajouter avec succès.");

                        return $this->redirectToRoute('user_project_list');
                    }
                } 

            }
            
        }

        return $this->render('pages/user/project/edit.html.twig', [
            "form" => $form->createView(),
            "project" => $project
        ]);
    }

    #[Route('/user/project/{id<\d+>}/delete', name: 'user_project_delete', methods: ["POST"])]
    public function delete(Project $project, Request $request): Response
    {

        // On controle si le projet que l'utilisateur souhaite supprimer soit bien son projet
        if ($this->getUser() != $project->getUser()) {
            return $this->redirectToRoute('user_project_list');
        }

        if ($this->isCsrfTokenValid('delete_project_' . $project->getId(), $request->request->get('_csrf_token'))) {
            // Si le token est valide

            $this->addFlash("danger", "Le projet a été supprimer");

            // On fait appel a entityManager pour préparer la requete
            $this->entityManager->remove($project);

            // On fait appel a entityManager pour exécuter la requete
            $this->entityManager->flush();
        }

        return $this->redirectToRoute("user_project_list");
    }

    #[Route('/visitor/project/{id<\d+>}/show', name: 'user_project_show', methods: ["GET","POST"])]
    public function show(Project $project, Request $request): Response
    {   

        /**
         * Récupérons l'utilisateur connecté
         * 
         * @var User
         */

        // On récupere les données de l'utilisateur connecté
        $user = $this->getUser();

        // On instancie un nouvelle objet Project
        $comment = new Comment();

        // Création du formulaire
        $form = $this->createForm(CommentFormType::class, $comment);

        // On récupere les donnés renvoyer après avoir rempli le formulaire
        $form->handleRequest($request);

        // On test la validation du formulaire
        if ($form->isSubmitted() && $form->isValid()) 
        {

            if ( $user == null )
            {
                $this->addFlash("warning", "Vous devez être connecté pour ajouter un commentaire.");
            }
            else {

                // On ajoute la date de création et de modification
                $comment->setCreatedAt(new DateTimeImmutable());
                $comment->setUpdatedAt(new DateTimeImmutable());      
                
                // On ajoute l'utilisateur qui a ajouter le commentaire
                $comment->setUser($user);   
                
                // On ajoute le projet sur le quel on commente
                $comment->setProject($project);    

                
                // On ajoute l'objet en BDD
                $this->entityManager->persist($comment);
                $this->entityManager->flush();

                $this->addFlash("success", "Le commentaire a été ajouter avec succès.");

                return $this->redirectToRoute("user_project_show", [
                    "id" => $project->getId(),
                    "comments" => $this->commentRepository->findBy(array("project" => $project->getId()),array('createdAt'=>'DESC')),
                    "project" => $this->projectRepository->findBy(array("id" => $project->getId())),
                    "userParticipates" => ( $user ? $this->projectUserRepository->findBy(array("project" => $project->getId(), "user" => $user->getId())) : 0)
                ]);

            }

        }

        return $this->render('pages/visitor/project/show.html.twig', [
            "form" => $form,
            "comments" => $this->commentRepository->findBy(array("project" => $project->getId()),array('createdAt'=>'DESC')),
            "project" => $this->projectRepository->findBy(array("id" => $project->getId())),
            "userParticipates" => ( $user ? $this->projectUserRepository->findBy(array("project" => $project->getId(), "user" => $user->getId())) : 0)
        ]);
    }

    #[Route('/user/project/{id<\d+>}/join', name: 'user_project_join', methods: ["POST"])]
    public function join(Project $project, Request $request): Response
    {

        // On instancie l'objet ProjectUser
        $projectUser = new ProjectUser;

        /**
         * Récupérons l'utilisateur connecté
         * 
         * @var User
         */

        // On récupere les données de l'utilisateur connecté
        $user = $this->getUser();

        // On récupère l'id de utilisateur a ajouter
        $projectUser->setProject($project);
        // On récupère l'id du projet a ajouter
        $projectUser->setUser($user);

        // Si le csrf token est valide
        if ($this->isCsrfTokenValid('join_project_' . $project->getId(), $request->request->get('_csrf_token'))) {
            
            // On vérifie si le projet est terminé
            if ($project->getEndDate() != null ){
                if ( $project->getEndDate() < new DateTimeImmutable)
                {
                    // Message flash
                    $this->addFlash("warning", "Le projet est terminé.");

                    return $this->redirectToRoute("user_project_show", [
                        "id" => $project->getId(),
                        "comments" => $this->commentRepository->findBy(array("project" => $project->getId()),array('createdAt'=>'DESC')),
                        "project" => $this->projectRepository->findBy(array("id" => $project->getId())),
                        "userParticipates" => ( $user ? $this->projectUserRepository->findBy(array("project" => $project->getId(), "user" => $user->getId())) : 0)
                    ]);
                }
            }

            // On vérifie si l'utilisateur participe deja au project
            if (count($this->projectUserRepository->findBy(array("project" => $project->getId(), "user" => $user->getId()))) == 0) {
                // Si le nombre d'entrées est égale à 0 l'utilisateur ne participe pas encore a ce projet
                

                // Si le projet dispose de place limité
                if ( $project->getAvailableSpace() != null )
                {
                    // On récupère combien de personne participe deja au projet et on vérifi s'il reste de la place
                    if (count($this->projectUserRepository->findBy(array("project" => $project->getId()))) == $project->getAvailableSpace())
                    {

                        // Message flash
                        $this->addFlash("warning", "Impossible de participer au projet, le nombre de places disponibles est atteint. Contactez directement l'organisateur du projet pour savoir s'il serait possible d'obtenir plus de places.");

                        return $this->redirectToRoute("user_project_show", [
                            "id" => $project->getId(),
                            "comments" => $this->commentRepository->findBy(array("project" => $project->getId()),array('createdAt'=>'DESC')),
                            "project" => $this->projectRepository->findBy(array("id" => $project->getId())),
                            "userParticipates" => ( $user ? $this->projectUserRepository->findBy(array("project" => $project->getId(), "user" => $user->getId())) : 0)
                        ]);

                    }

                }

                // Utilisation de l'entity manager pour préparer la requete
                $this->entityManager->persist($projectUser);

                // Message flash
                $this->addFlash("success", "Votre participation au projet a été enregistrée.");
            } else {
                // Sinon l'utilisateur participe déjà au projet

                // On retire le
                $this->entityManager->remove($this->projectUserRepository->findBy(array("project" => $project->getId(), "user" => $user->getId()))[0]);

                // Message flash
                $this->addFlash("success", "Votre participation au projet a été retirée.");
            }

            // On utilise l'entity manager pour exécuter la requête préparer
            $this->entityManager->flush();
        }

        return $this->redirectToRoute("user_project_show", [
            "id" => $project->getId(),
            "comments" => $this->commentRepository->findBy(array("project" => $project->getId()),array('createdAt'=>'DESC')),
            "project" => $this->projectRepository->findBy(array("id" => $project->getId())),
            "userParticipates" => ( $user ? $this->projectUserRepository->findBy(array("project" => $project->getId(), "user" => $user->getId())) : 0)
        ]);

    }

    #[Route('/user/comment/{id<\d+>}/{id_project<\d+>}/edit', name: 'user_comment_edit', methods: ["GET","POST"])]
    public function editComment(int $id, int $id_project, Request $request): Response
    {
        
        // Récupérez le commentaire et le projet en utilisant les identifiants
        $comment = $this->entityManager->getRepository(Comment::class)->find($id);
        $project = $this->entityManager->getRepository(Project::class)->find($id_project);

        /**
         * Récupérons l'utilisateur connecté
         * 
         * @var User
         */

        // On récupere les données de l'utilisateur connecté
        $user = $this->getUser();

        // On controle si le commentaire que l'utilisateur souhaite modifier soit bien son commentaire
        if ( $project == null)
        {
            return $this->redirectToRoute("app_map");
        }
        elseif ( $comment == null )
        {
            return $this->redirectToRoute("user_project_show", [
                "id" => $project->getId(),
                "comments" => $this->commentRepository->findBy(array("project" => $project->getId()),array('createdAt'=>'DESC')),
                "project" => $this->projectRepository->findBy(array("id" => $project->getId())),
                "userParticipates" => ( $user ? $this->projectUserRepository->findBy(array("project" => $project->getId(), "user" => $user->getId())) : 0)
            ]);
        }
        elseif ( $comment->getProject()->getId() != $project->getId() || $this->getUser() != $comment->getUser()) {
            return $this->redirectToRoute("user_project_show", [
                "id" => $project->getId(),
                "comments" => $this->commentRepository->findBy(array("project" => $project->getId()),array('createdAt'=>'DESC')),
                "project" => $this->projectRepository->findBy(array("id" => $project->getId())),
                "userParticipates" => ( $user ? $this->projectUserRepository->findBy(array("project" => $project->getId(), "user" => $user->getId())) : 0)
            ]);
        } else {}

        // Création du formulaire
        $form = $this->createForm(CommentFormType::class, $comment);

        // On récupere les donnés renvoyer après avoir rempli le formulaire
        $form->handleRequest($request);

        // On test la validation du formulaire
        if ($form->isSubmitted() && $form->isValid()) 
        {

            // On ajoute la date de création et de modification
            $comment->setUpdatedAt(new DateTimeImmutable());      
                
            // On ajoute l'objet en BDD
            $this->entityManager->persist($comment);
            $this->entityManager->flush();

            $this->addFlash("success", "Le commentaire a été modifier avec succès.");

            return $this->redirectToRoute("user_project_show", [
                    "id" => $project->getId(),
                    "comments" => $this->commentRepository->findBy(array("project" => $project->getId()),array('createdAt'=>'DESC')),
                    "project" => $this->projectRepository->findBy(array("id" => $project->getId())),
                    "userParticipates" => ( $user ? $this->projectUserRepository->findBy(array("project" => $project->getId(), "user" => $user->getId())) : 0)
            ]);

        }

        return $this->render('pages/user/comment/edit.html.twig', [
            "form" => $form->createView(),
            "comment" => $comment
        ]);
    }

    #[Route('/user/comment/{id<\d+>}/{id_project<\d+>}/delete', name: 'user_comment_delete', methods: ["POST"])]
    public function deleteComment(int $id, int $id_project, Request $request): Response
    {

        // Récupérez le commentaire et le projet en utilisant les identifiants
        $comment = $this->entityManager->getRepository(Comment::class)->find($id);
        $project = $this->entityManager->getRepository(Project::class)->find($id_project);

        /**
         * Récupérons l'utilisateur connecté
         * 
         * @var User
         */

        // On récupere les données de l'utilisateur connecté
        $user = $this->getUser();

        // On controle si le commentaire que l'utilisateur souhaite modifier soit bien son commentaire
        if ( $project == null)
        {
            return $this->redirectToRoute("app_map");
        }
        elseif ( $comment == null )
        {
            return $this->redirectToRoute("user_project_show", [
                "id" => $project->getId(),
                "comments" => $this->commentRepository->findBy(array("project" => $project->getId()),array('createdAt'=>'DESC')),
                "project" => $this->projectRepository->findBy(array("id" => $project->getId())),
                "userParticipates" => ( $user ? $this->projectUserRepository->findBy(array("project" => $project->getId(), "user" => $user->getId())) : 0)
            ]);
        }
        elseif ( $comment->getProject()->getId() != $project->getId() || $this->getUser() != $comment->getUser()) {
            return $this->redirectToRoute("user_project_show", [
                "id" => $project->getId(),
                "comments" => $this->commentRepository->findBy(array("project" => $project->getId()),array('createdAt'=>'DESC')),
                "project" => $this->projectRepository->findBy(array("id" => $project->getId())),
                "userParticipates" => ( $user ? $this->projectUserRepository->findBy(array("project" => $project->getId(), "user" => $user->getId())) : 0)
            ]);
        } else {}


        if ($this->isCsrfTokenValid('delete_comment_' . $comment->getId(), $request->request->get('_csrf_token'))) {
            // Si le token est valide

            $this->addFlash("danger", "Le commentaire a été supprimer");

            // On fait appel a entityManager pour préparer la requete
            $this->entityManager->remove($comment);

            // On fait appel a entityManager pour exécuter la requete
            $this->entityManager->flush();
        }

        return $this->redirectToRoute("user_project_show", [
            "id" => $project->getId(),
            "comments" => $this->commentRepository->findBy(array("project" => $project->getId()),array('createdAt'=>'DESC')),
            "project" => $this->projectRepository->findBy(array("id" => $project->getId())),
            "userParticipates" => ( $user ? $this->projectUserRepository->findBy(array("project" => $project->getId(), "user" => $user->getId())) : 0)
        ]);
    }

}
