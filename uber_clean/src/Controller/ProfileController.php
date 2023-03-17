<?php

namespace App\Controller;

use App\Entity\Cleaner;
use App\Form\RegistrationCleanerFormType;
use App\Form\RegistrationCustomerFormType;
use App\Repository\CleanerRepository;
use App\Repository\CustomerRepository;
use App\Service\FileUploader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

class ProfileController extends AbstractController
{
    #[Route('/profile', name: 'app_profile')]
    public function index(CustomerRepository $customerRepository, CleanerRepository $cleanerRepository): Response
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        $customer = $customerRepository->findOneBy(['email' => $user->getUserIdentifier()]);
        $cleaner = $cleanerRepository->findOneBy(['email' => $user->getUserIdentifier()]);

        $userType = $customer ?? $cleaner;

        return $this->render('profile/index.html.twig', [
            'controller_name' => 'ProfileController',
            'user' => $user,
            'userType' => $userType,
            'isCleaner' => $user instanceof Cleaner,
        ]);
    }

    #[Route('/profile/edit', name: 'app_edit_profile')]
    public function editProfile(CustomerRepository $customerRepository, CleanerRepository $cleanerRepository, Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager, FileUploader $fileUploader): Response
    {
        $user = $this->getUser();

        if (!$user || !($user instanceof PasswordAuthenticatedUserInterface)) {
            return $this->redirectToRoute('app_login');
        }
        $customer = $customerRepository->findOneBy(['email' => $user->getUserIdentifier()]);
        $cleaner = $cleanerRepository->findOneBy(['email' => $user->getUserIdentifier()]);

        if ($customer) {
            $form = $this->createForm(RegistrationCustomerFormType::class, $user);
            $form->handleRequest($request);
        }
        else {
            $form = $this->createForm(RegistrationCleanerFormType::class, $user);
            $form->handleRequest($request);
        }

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password

            if ($customer) {
                $customer->setPassword(
                    $userPasswordHasher->hashPassword(
                        $user,
                        $form->get('plainPassword')->getData()
                    )
                );
                $image = $form->get('image')->getData();
                if ($image) {
                    $fileName = $fileUploader->upload($image);
                    $customer->setImage($fileName);
                }
                $entityManager->persist($customer);
            }
            elseif ($cleaner) {
                $cleaner->setPassword(
                    $userPasswordHasher->hashPassword(
                        $user,
                        $form->get('plainPassword')->getData()
                    )
                );

                $image = $form->get('image')->getData();
                if ($image) {
                    $fileName = $fileUploader->upload($image);
                    $cleaner->setImage($fileName);
                }
                $entityManager->persist($cleaner);
            }

            $entityManager->flush();
            //$flashBag->add('success', 'Profil mis à jour avec succès!');

            $this->addFlash(
                'notice',
                'Profil mis à jour avec succès!',
            );

            return $this->redirectToRoute('app_profile');
        }

        $userType = $customer ?? $cleaner;

        return $this->render('profile/editProfile.html.twig', [
            'userType' => $userType,
            'user' => $user,
            'registrationForm' => $form->createView(),
            'isCleaner' => $user instanceof Cleaner,
        ]);
    }
}
