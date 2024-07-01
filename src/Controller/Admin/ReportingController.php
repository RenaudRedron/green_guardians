<?php

namespace App\Controller\Admin;

use App\Entity\Reporting;
use App\Repository\ReportingRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/modo')]
class ReportingController extends AbstractController
{

    public function __construct(
        private EntityManagerInterface $entityManager,
        private ReportingRepository $reportingRepository
    ) {
    }

    #[Route('/reporting/list', name: 'admin_reporting_index')]
    public function index(): Response
    {
        
        return $this->render('pages/admin/reporting/index.html.twig', [
            "reporting" => $this->reportingRepository->findAll()
        ]);
    }

    #[Route('/reporting/{id<\d+>}/delete', name: 'admin_reporting_delete')]
    public function delete(Reporting $reporting, Request $request): Response
    {
        if ($this->isCsrfTokenValid('delete_report_' . $reporting->getId(), $request->request->get('_csrf_token'))) {
            // Si le token est valide

            $this->addFlash("danger", "Le signalement a été supprimer");

            // On fait appel a entityManager pour préparer la requete
            $this->entityManager->remove($reporting);

            // On fait appel a entityManager pour exécuter la requete
            $this->entityManager->flush();
        }

        return $this->redirectToRoute("admin_reporting_index");
    }
}
