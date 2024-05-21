<?php

namespace App\Controller\User;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/projet')]
class ProjetController extends AbstractController
{
    #[Route('', name: 'app_projet')]
    public function index(): Response
    {
        return $this->render('pages/user/projet/index.html.twig');
    }
}
