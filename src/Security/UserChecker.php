<?php
namespace App\Security;

use App\Entity\User as AppUser;
use App\Repository\UserRepository;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\Exception\AccountExpiredException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAccountStatusException;

class UserChecker implements UserCheckerInterface
{

    public function __construct(
        private UserRepository $userRepository,
    ) {
    }

    public function checkPreAuth(UserInterface $user): void
    {
        if (!$user instanceof AppUser) {
            return;
        }

        $checkUser = $this->userRepository->findBy(array("id" => $user->getId()));

        // On vérifie si le compte est banni
        if($checkUser[0]->isBanned())
        {
            throw new CustomUserMessageAccountStatusException('Cet utilisateur a été banni en raison d\'une violation des règles.');

        }

        // On vérifie ssi le compte est vérifié
        if(!$checkUser[0]->isVerified())
        {
            throw new CustomUserMessageAccountStatusException('Un email a été envoyé à l\'adresse email du compte pour la vérifier.');
        }
    }

    public function checkPostAuth(UserInterface $user): void
    {
        if (!$user instanceof AppUser) {
            return;
        }
    }
}