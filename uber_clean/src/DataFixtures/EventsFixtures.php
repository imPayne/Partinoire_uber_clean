<?php

namespace App\DataFixtures;

use App\Entity\Service;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class EventsFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {

        $service1 = new Service();
        $service1->setName("Vitres");
        $service1->setIcon("vitres.jpeg");

        $service2 = new Service();
        $service2->setName("Linges");
        $service2->setIcon("linges.jpg");

        $service3 = new Service();
        $service3->setName("Repassage");
        $service3->setIcon("repassage.jpg");

        $service4 = new Service();
        $service4->setName("Vaisselles");
        $service4->setIcon("vaisselles.jpg");

        $service5 = new Service();
        $service5->setName("Pelouse");
        $service5->setIcon("pelouse.jpg");

        $manager->persist($service1);
        $manager->persist($service2);
        $manager->persist($service3);
        $manager->persist($service4);
        $manager->persist($service5);

        $manager->flush();
    }
}
