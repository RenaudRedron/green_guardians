<?php

namespace App\DataFixtures;

use App\Entity\User;
use DateTimeImmutable;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class SuperAdminFixtures extends Fixture
{

    public function __construct(private UserPasswordHasherInterface $hasher)
    {
        
    }

    public function load(ObjectManager $manager): void
    {
        $superAdmin = $this->createSuperAdmin();

        $manager->persist($superAdmin);

        $manager->flush();
    }

    /**
     * Le rôle de cette méthode est de créer le super admin
     *
     * @return User
     */
    private function createSuperAdmin() : User
    {

        $superAdmin = new User();

        $passwordHashed = $this->hasher->hashPassword($superAdmin, "azerty12345.A");

        $superAdmin 
                    ->setNickname("Admin")
                    ->setFirstName("Renaud")
                    ->setLastName("Redron")
                    ->setEmail("renaud.redron@gmail.com")
                    ->setPhone("0603332923")
                    ->setBirth(new DateTimeImmutable('09/05/1989'))
                    ->setRoles(["ROLE_SUPER_ADMIN","ROLE_ADMIN","ROLE_USER"])
                    ->setPassword($passwordHashed)
                    ->setVerified(true)
                    ->setCreatedAt(new DateTimeImmutable())
                    ->setUpdatedAt(new DateTimeImmutable())
        ;

        return $superAdmin;
    }

}
