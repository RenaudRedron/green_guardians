<?php

namespace App\Controller\User;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/user')]
class ProfileController extends AbstractController
{
    #[Route('/profile', name: 'user_profile')]
    public function index(): Response
    {   

        $user = $this->getUser();

        return $this->render('pages/user/profile/index.html.twig', [
            "user" => $user
        ]);
    }
}
