<?php

namespace App\DataFixtures;

use App\Entity\Cleaner;
use App\Entity\Customer;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    private UserPasswordHasherInterface $hasher;

    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->hasher = $hasher;
    }

    // ...
    public function load(ObjectManager $manager)
    {
        $user = new Customer();
        $user->setFirstName('admin');
        $user->setEmail("admin@gmail.com");
        $user->setAdresse("2 rue de l'admin");
        $user->setCodePostal("35000");
        $user->setRegion("Bretagne");
        $user->setLastName("Administrator");
        $user->setRoles(['ROLE_CUSTOMER']);
        $user->setImage("admin-6439f330cc79a.png");
        $password = $this->hasher->hashPassword($user, 'admin');
        $user->setPassword($password);
        $manager->persist($user);

        $user2 = new Cleaner();
        $user2->setRoles(['ROLE_CLEANER']);
        $user2->setFirstName('Cleaner');
        $user2->setEmail('cleaner@gmail.com');
        $user2->setLastName('Néttoyeur');
        $user2->setImage("admin-6439f330cc79a.png");
        $user2->setNote(4.9);
        $password2 = $this->hasher->hashPassword($user2, 'cleaner');
        $user2->setPassword($password2);
        $manager->persist($user2);

        $manager->flush();
    }
}
