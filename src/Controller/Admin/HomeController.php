<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin')]
class HomeController extends AbstractController
{
    #[Route('/home', name: 'admin_home')]
    public function index(): Response
    {
    
        return $this->render('pages/admin/home/index.html.twig', [
            'controller_name' => 'AdminController',
        ]);
    }
}
