<?php

namespace App\Controller\Admin;

use App\Entity\User;
use DateTimeImmutable;
use App\Entity\Project;
use App\Form\UserProjectFormType;
use App\Repository\CommentRepository;
use App\Repository\ProjectRepository;
use App\Repository\ProjectUserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/modo')]
class ProjectController extends AbstractController
{

    public function __construct(
        private EntityManagerInterface $entityManager,
        private ProjectRepository $projectRepository,
        private ProjectUserRepository $projectUserRepository,
        private CommentRepository $commentRepository
    ) {
    }

    #[Route('/project/list', name: 'admin_project_index')]
    public function index(): Response
    {
        /**
         * Récupérons l'utilisateur connecté
         * 
         * @var User
         */

        // On récupere les données de l'utilisateur connecté
        $user = $this->getUser();

        return $this->render('pages/admin/project/index.html.twig', [
            "projects" => $this->projectRepository->findAll(),
            "comments" => $this->commentRepository->findAll(),
            "userParticipates" => ( $user ? $this->projectUserRepository->findAll() : 0)
        ]);
    }

    #[Route('/project/{id<\d+>}/publish', name: 'admin_project_publish', methods: ["POST"])]
    public function publish(Project $project, Request $request): Response
    {
        // Si le csrf token est valide
        if ($this->isCsrfTokenValid('publish_project_' . $project->getId(), $request->request->get('_csrf_token'))) {
            // Si l'article n'a pas encore été publié
            if (false === $project->isPublished()) {
                // On publie l'article
                $project->setPublished(true);

                // Mise à jour de la date de modification
                $project->setUpdatedAt(new DateTimeImmutable());

                // Mise à jour de la date de publication
                $project->setPublishedAt(new DateTimeImmutable());

                // Utilisation de l'entity manager pour préparer la requete
                $this->entityManager->persist($project);

                // Message flash
                $this->addFlash("success", "Le projet a été publié.");
            } else {

                // On retire la publication de l'article
                $project->setPublished(false);

                // Mise à jour de la date de modification
                $project->setUpdatedAt(new DateTimeImmutable());

                // Mise à jour de la date de publication
                $project->setPublishedAt(null);

                // Utilisation de l'entity manager pour préparer la requete
                $this->entityManager->persist($project);

                // Message flash
                $this->addFlash("success", "Le projet a été retiré de la liste des publications.");
            }

            // On utilise l'entity manager pour exécuter la requête préparer
            $this->entityManager->flush($project);
        }

        // Redirection et on arrete l'execution du script avec return
        return $this->redirectToRoute("admin_project_index");
    }

    #[Route('/project/{id<\d+>}/edit', name: 'admin_project_edit', methods: ["GET","POST"])]
    public function edit(Project $project,  Request $request): Response
    {

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

                        $this->addFlash("success", "Le projet a été modifier avec succès.");

                        return $this->redirectToRoute('admin_project_index');
                    }
                } 

            }
            
        }

        return $this->render('pages/admin/project/edit.html.twig', [
            "form" => $form->createView(),
            "project" => $project
        ]);
    }

    #[Route('/project/{id<\d+>}/delete', name: 'admin_project_delete', methods: ["POST"])]
    public function delete(Project $project, Request $request): Response
    {

        if ($this->isCsrfTokenValid('delete_project_' . $project->getId(), $request->request->get('_csrf_token'))) {
            // Si le token est valide

            $this->addFlash("danger", "Le projet a été supprimer");

            // On fait appel a entityManager pour préparer la requete
            $this->entityManager->remove($project);

            // On fait appel a entityManager pour exécuter la requete
            $this->entityManager->flush();
        }

        return $this->redirectToRoute("admin_project_index");
    }


}
