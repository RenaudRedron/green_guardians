<?php

namespace App\Controller\User;

use DateTimeImmutable;
use App\Entity\Project;
use App\Form\UserProjectFormType;
use App\Repository\ProjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/user')]
class ProjectController extends AbstractController
{

    public function __construct(
        private EntityManagerInterface $entityManager,
        private ProjectRepository $projectRepository
    ) {
    }

    #[Route('/project/list', name: 'user_project_list')]
    public function index(): Response
    {   
        return $this->render('pages/user/project/index.html.twig', [
            "projects" => $this->projectRepository->findAll()
        ]);
    }

    #[Route('/project/create', name: 'user_project_create')]
    public function create(Request $request): Response
    {

        $project = new Project();

        $form = $this->createForm(UserProjectFormType::class, $project);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // Mise en forme de l'adresse pour l'envoi a l'api via curl
            $street = str_replace(" ", "+", $project->getStreet());
            $code = $project->getCode();
            $city = str_replace(" ", "+", $project->getCity());;

            // Envoi a l'api
            $curl = curl_init("http://api-adresse.data.gouv.fr/search/?q={$street}+{$code}+{$city}");
            curl_setopt_array($curl, [
                CURLOPT_TIMEOUT => 1,
                CURLOPT_RETURNTRANSFER => true
            ]);

            // Récupération des information
            $data = curl_exec($curl);

            // Transformation du json en tableau
            $json = json_decode($data, true);

            // Si il existe aucune adresse avec les information entrés
            if (count($json["features"]) == 0 || $json["features"] == null) {

                // Message d'erreur
                $this->addFlash("warning", "Cette adresse est introuvable");
            } else {

                $lat = $json["features"][0]["geometry"]["coordinates"][1];
                $project->setLatitude($lat);

                $lng = $json["features"][0]["geometry"]["coordinates"][0];
                $project->setLongitude($lng);

                $address = $project->getStreet() . ', ' . $project->getCode() . ' ' . $project->getCity();
                $project->setAddress($address);

                $project->setCreatedAt(new DateTimeImmutable());
                $project->setUpdatedAt(new DateTimeImmutable());
    
                // Si la date de fin est plutôt que la date de début ou si la date de fin est avant la date actuelle
                if ($project->getEndDate() != null && ($project->getStartDate() > $project->getEndDate() || (new DateTimeImmutable()) > $project->getEndDate())) { 
                    $this->addFlash("warning", "La date de fin choisi pour le projet est incorrect.");
                }
                else {

                    $this->entityManager->persist($project);
                    $this->entityManager->flush();

                    $this->addFlash("success", "Le projet a été ajouter avec succès.");

                    return $this->redirectToRoute('user_project_list');

                }               

            }
        }

        return $this->render('pages/user/project/create.html.twig', [
            "form" => $form->createView()
        ]);
    }

    #[Route('/project/{id<\d+>}/edit', name: 'user_project_edit')]
    public function edit(Project $project,  Request $request): Response
    {

        $form = $this->createForm(UserProjectFormType::class, $project);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // Mise en forme de l'adresse pour l'envoi a l'api via curl
            $street = str_replace(" ", "+", $project->getStreet());
            $code = $project->getCode();
            $city = str_replace(" ", "+", $project->getCity());;

            // Envoi a l'api
            $curl = curl_init("http://api-adresse.data.gouv.fr/search/?q={$street}+{$code}+{$city}");
            curl_setopt_array($curl, [
                CURLOPT_TIMEOUT => 1,
                CURLOPT_RETURNTRANSFER => true
            ]);

            // Récupération des information
            $data = curl_exec($curl);

            // Transformation du json en tableau
            $json = json_decode($data, true);

            // Si il existe aucune adresse avec les information entrés
            if (count($json["features"]) == 0 || $json["features"] == null) {

                // Message d'erreur
                $this->addFlash("warning", "Cette adresse est introuvable");
            } else {

                $lat = $json["features"][0]["geometry"]["coordinates"][1];
                $project->setLatitude($lat);

                $lng = $json["features"][0]["geometry"]["coordinates"][0];
                $project->setLongitude($lng);

                $address = $project->getStreet() . ', ' . $project->getCode() . ' ' . $project->getCity();
                $project->setAddress($address);

                $project->setUpdatedAt(new DateTimeImmutable());
    
                // Si la date de fin est plutôt que la date de début ou si la date de fin est avant la date actuelle
                if ($project->getEndDate() != null && ($project->getStartDate() > $project->getEndDate() || (new DateTimeImmutable()) > $project->getEndDate())) { 
                    $this->addFlash("warning", "La date de fin choisi pour le projet est incorrect.");
                }
                else {

                    $this->entityManager->persist($project);
                    $this->entityManager->flush();

                    $this->addFlash("success", "Le projet a été modifier avec succès.");

                    return $this->redirectToRoute('user_project_list');

                }               

            }
        }

        return $this->render('pages/user/project/edit.html.twig', [
            "form" => $form->createView(),
            "project" => $project
        ]);
    }

    #[Route('/project/{id<\d+>}/delete', name: 'user_project_delete', methods: ["POST"])]
    public function delete(Project $project, Request $request): Response
    {  
        if ( $this->isCsrfTokenValid('delete_project_'.$project->getId(), $request->request->get('_csrf_token') ) )
        {
            // Si le token est valide

            $this->addFlash("danger", "Le projet a été supprimer");

            // On fait appel a entityManager pour préparer la requete
            $this->entityManager->remove($project);

            // On fait appel a entityManager pour exécuter la requete
            $this->entityManager->flush();

        }

        return $this->redirectToRoute("user_project_list");
    }
}
