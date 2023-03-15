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
        $user->setFirstName('hugo');
        $user->setEmail("hmonchab@gmail.com");
        $user->setAdresse("2 rue du petit marteau");
        $user->setCodePostal("35000");
        $user->setRegion("Bretagne");
        $user->setLastName("mcn");
        $user->setRoles(['ROLE_CUSTOMER']);
        $password = $this->hasher->hashPassword($user, '123456');
        $user->setPassword($password);
        $manager->persist($user);

        $user2 = new Cleaner();
        $user2->setRoles(['ROLE_CLEANER']);
        $user2->setFirstName('léo');
        $user2->setEmail('leo@gmail.com');
        $user2->setLastName('mcn');
        $password2 = $this->hasher->hashPassword($user2, '123456');
        $user2->setPassword($password2);
        $manager->persist($user2);

        $manager->flush();
    }
}
