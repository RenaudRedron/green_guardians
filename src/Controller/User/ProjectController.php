<?php

namespace App\Controller\User;

use App\Entity\User;
use DateTimeImmutable;
use App\Entity\Project;
use App\Form\UserProjectFormType;
use App\Repository\ProjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ProjectController extends AbstractController
{

    public function __construct(
        private EntityManagerInterface $entityManager,
        private ProjectRepository $projectRepository
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
            "projects" => $this->projectRepository->findBy(array("user" => $user->getId())) // On envoi les projets qui appartient a l'user connecté
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

    #[Route('/visitor/project/{id<\d+>}/show', name: 'user_project_show', methods: ["GET"])]
    public function show($id): Response
    {   


        return $this->render('pages/visitor/project/show.html.twig', [
            "project" => $this->projectRepository->findBy(array("id" => $id))
        ]);
    }
}
