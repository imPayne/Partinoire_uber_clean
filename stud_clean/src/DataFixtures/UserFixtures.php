<?php

namespace App\DataFixtures;

use App\Entity\Cleaner;
use App\Entity\Customer;
use App\Entity\Housework;
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
        $user->setEmail("hugo@gmail.com");
        $user->setAdresse("2 rue de la cacahouète");
        $user->setCodePostal("35200");
        $user->setRegion("Bretagne");
        $user->setLastName("Développeur");
        $user->setRoles(['ROLE_CUSTOMER']);
        $user->setImage("gabimaru-anime-646aa6dcccd16.gif");
        $user->setPhoneNumber("0772378558");
        $password = $this->hasher->hashPassword($user, '5445HUhu00');
        $user->setPassword($password);

        $admin = new User();
        $admin->setFirstName("admin");
        $admin->setLastName("administrateur");
        $admin->setImage("5df0fa20efbbfc61d1c419fb42b9c4670f1610cdr1-540-304-hq-6463dfe6ed33b.gif");
        $admin->setRoles("ROLE_ADMIN");
        $admin->setPassword($this->hasher->hashPassword($admin, "admin"));
        $admin->setEmail("admin@gmail.com");
        $manager->persist($admin);


        $newHousework = new Housework();
        $newHousework->setPrice(40);
        $newHousework->setTitle("nettoyage appartement");
        $newHousework->setDescription("Bonjour, j'ai besoin de quelqu'un pour venir réaliser le ménage dans mon appartement de 80m²");
        $newHousework->setListImage("appart80.jpg");
        $dateString = '2023-06-20';
        $dateStart = \DateTime::createFromFormat('Y-m-d', $dateString);
        $newHousework->setDateStart($dateStart);
        $hour = new \DateTime('17:00');
        $newHousework->setHour($hour);
        $user->addHousework($newHousework);
        $newHousework->setCustomer($user);
        $manager->persist($newHousework);
        $manager->persist($user);

        $user2 = new Cleaner();
        $user2->setRoles(['ROLE_CLEANER']);
        $user2->setStudentProof('tumblr-o2wotyHcxt1t0l1jvo1-500-64417cedda35a.gif');
        $user2->setFirstName('Cleaner');
        $user2->setEmail('cleaner@gmail.com');
        $user2->setLastName('Néttoyeur');
        $user2->setImage("reapermedal.png");
        $user2->setPhoneNumber("0747247418");
        $user2->setNote(4.9);
        $password2 = $this->hasher->hashPassword($user2, 'cleaner');
        $user2->setPassword($password2);
        $manager->persist($user2);

        $manager->flush();
    }
}
