<?php

namespace App\Controller;

use App\Entity\Customer;
use App\Entity\Housework;
use App\Form\MenagePartyFormType;
use App\Repository\CustomerRepository;
use App\Repository\HouseworkRepository;
use App\Service\FileUploader;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class HouseworkController extends AbstractController
{
    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    #[Route('/housework', name: 'app_housework')]
    public function index(FileUploader $fileUploader, SessionInterface $session, Request $request, CustomerRepository $customerRepository, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        if (!$user) {
            return ($this->redirectToRoute('app_login'));
        }
        $newHousework = new Housework();

        $form = $this->createForm(MenagePartyFormType::class, $newHousework);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $customer = $customerRepository->findOneBy(['email' => $user->getUserIdentifier()]);
            if ($customer) {
                $newHousework->setDateStart($form->get('dateStart')->getData()); // passe l'objet DateTime à setDateStart()
                $newHousework->setDescription($form->get('description')->getData());
                $newHousework->setTitle($form->get('title')->getData());
                $image = $form->get('listImage')->getData();
                $fileName = $fileUploader->upload($image);
                $newHousework->setListImage($fileName);
                $customer->addHousework($newHousework);

                $entityManager->persist($customer);

                $entityManager->persist($newHousework);
                $entityManager->flush();

                return $this->redirectToRoute("app_profile");
            }
        }

        return $this->render('housework/index.html.twig', [
            'controller_name' => 'HouseworkController',
            'form' => $form->createView(),
        ]);
    }
    #[Route('/my_houseworks', name: 'app_show_houseworks')]
    public function myHouseworks(Request $request, CustomerRepository $customerRepository, EntityManagerInterface $entityManager, HouseworkRepository $houseworkRepository): Response
    {
        $user = $this->getUser();
        if (!$user) {
            return ($this->redirectToRoute('app_login'));
        }
        $customer = $customerRepository->findOneBy(['email' => $this->getUser()]);

        return $this->render('housework/myHouseworks.html.twig', [
            'controller_name' => 'HouseworkController',
            'customer' => $customer,
        ]);
    }
}
