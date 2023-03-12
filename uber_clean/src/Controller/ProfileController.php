<?php

namespace App\Controller;

use App\Form\RegistrationFormType;
use App\Repository\CleanerRepository;
use App\Repository\CustomerRepository;
use App\Service\FileUploader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

class ProfileController extends AbstractController
{
    #[Route('/profile', name: 'app_profile')]
    public function index(): Response
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }
        return $this->render('profile/index.html.twig', [
            'controller_name' => 'ProfileController',
            'user' => $user,
        ]);
    }

    #[Route('/profile/edit', name: 'app_edit_profile')]
    public function editProfile(FlashBagInterface $flashBag, CustomerRepository $customerRepository, CleanerRepository $cleanerRepository, Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager, FileUploader $fileUploader): Response
    {
        $user = $this->getUser();

        if (!$user || !($user instanceof PasswordAuthenticatedUserInterface)) {
            return $this->redirectToRoute('app_login');
        }
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );
            $image = $form->get('image')->getData();
            if ($image) {
                $fileName = $fileUploader->upload($image);
                $user->setImage($fileName);
            }
            $customer = $customerRepository->findOneBy(['email' => $user->getUserIdentifier()]);
            $cleaner = $cleanerRepository->findOneBy(['email' => $user->getUserIdentifier()]);;
            $cleaner->setLastName($user->getLastName());
            $cleaner->setFirstName($user->getFirstName());
            $cleaner->setImage($user->getImage());
            $cleaner->setEmail($user->getEmail());
            $cleaner->setPassword($user->getPassword());
            $cleaner->setRoles($user->getRoles());
            $customer->setEmail($user->getEmail());
            $customer->setPassword($user->getPassword());
            $customer->setRoles($user->getRoles());
            $customer->setFirstName($user->getFirstName());
            $customer->setImage($user->getImage());
            $customer->setLastName($user->getLastName());

            $entityManager->persist($user);
            $entityManager->persist($cleaner);
            $entityManager->persist($customer);

            $entityManager->flush();
            //$flashBag->add('success', 'Profil mis à jour avec succès!');

            $this->addFlash(
                'notice',
                'Profil mis à jour avec succès!',
            );

            return $this->redirectToRoute('app_profile');
        }

        //$messages = $flashBag->get('success');

        return $this->render('profile/editProfile.html.twig', [
            'user' => $user,
        ]);
    }
}
