<?php

namespace App\Controller;

use App\Entity\Cleaner;
use App\Form\RegistrationCleanerFormType;
use App\Form\RegistrationCustomerFormType;
use App\Repository\CleanerRepository;
use App\Repository\CustomerRepository;
use App\Repository\HouseworkRepository;
use App\Repository\ParticipantRepository;
use App\Service\FileUploader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
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

    #[Route('/profile/upcoming_performances', name: 'app_upcoming_performances')]
    public function prestations(HouseworkRepository $houseworkRepository, ParticipantRepository $participantRepository, CleanerRepository $cleanerRepository): Response
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }
        $cleaner = $cleanerRepository->findOneBy(['email' => $user->getUserIdentifier()]);

        $myUpcomingPerformances = $participantRepository->findBy(['Cleaner' => $cleaner]);

        $menagePartyRegistered = $houseworkRepository->findBy(['id' => $myUpcomingPerformances]);

        return $this->render('profile/myUpcomingPerformances.html.twig', [
            'controller_name' => 'ProfileController',
            'cleaner' => $cleaner,
            'menagePartyRegistered' => $menagePartyRegistered,
            'myUpcomingPerformances' => $myUpcomingPerformances,
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
            $form = $this->createForm(RegistrationCustomerFormType::class, $user, [
                'is_edit_profile' => true,
            ]);
            $form->handleRequest($request);
        }
        else {
            $form = $this->createForm(RegistrationCleanerFormType::class, $user, [
                'is_edit_profile' => true,
            ]);
            $form->handleRequest($request);
        }
        $currentPassword = $form->get('currentPassword')->getData();
        $userPassword = null;

        if ($form->isSubmitted() && $form->isValid()) {
            if ($customer) {
                $userPassword = $customer->getPassword();
                if (!password_verify($currentPassword, $userPassword)) {
                    $form->get('currentPassword')->addError(new FormError('Incorrecte!!'));
                }
                elseif (password_verify($currentPassword, $userPassword)) {
                    $image = $form->get('image')->getData();
                    if ($form->get('plainPassword')->getData()) {
                        $customer->setPassword(
                            $userPasswordHasher->hashPassword(
                                $user,
                                $form->get('plainPassword')->getData()
                            )
                        );
                        $entityManager->persist($customer);
                    }
                    if ($image) {
                        $fileName = $fileUploader->upload($image);
                        $customer->setImage($fileName);
                    }
                    $entityManager->persist($customer);
                }
            }

            elseif ($cleaner) {
                $userPassword = $cleaner->getPassword();
                $image = $form->get('image')->getData();
                if (!password_verify($currentPassword, $userPassword)) {
                    $form->get('currentPassword')->addError(new FormError('Incorrecte!!'));
                }
                elseif (password_verify($currentPassword, $userPassword)) {
                    if ($form->get('plainPassword')->getData()) {
                        $user->setPassword(
                            $userPasswordHasher->hashPassword(
                                $user,
                                $form->get('plainPassword')->getData()
                            )
                        );
                        $entityManager->persist($cleaner);
                    }
                    if ($image) {
                        $fileName = $fileUploader->upload($image);
                        $cleaner->setImage($fileName);
                    }
                    $student_proof = $form->get('student_proof')->getData();
                    if ($student_proof) {
                        $StudentProofFileName = $fileUploader->upload($student_proof);
                        $user->setStudentProof($StudentProofFileName);
                    }
                    $entityManager->persist($cleaner);
                }
            }
            $entityManager->flush();
            if (password_verify($currentPassword, $userPassword))
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
