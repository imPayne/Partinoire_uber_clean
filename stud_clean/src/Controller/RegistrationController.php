<?php

namespace App\Controller;

use App\Entity\Cleaner;
use App\Entity\Customer;
use App\Entity\User;
use App\Form\RegistrationCleanerFormType;
use App\Form\RegistrationCustomerFormType;
use App\Service\FileUploader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager, FileUploader $fileUploader): Response
    {
        return $this->render('registration/registrationChoice.html.twig', [
        ]);
    }


    #[Route('/registration/customer', name: 'app_registration_customer')]
    public function registerCustomer(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager, FileUploader $fileUploader): Response
    {
        $user = new Customer;
        $form = $this->createForm(RegistrationCustomerFormType::class, $user);
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

            $user->setRoles(["ROLE_CUSTOMER"]);

            $user->setPhoneNumber($form->get('phoneNumber')->getData());

            if ($form->get('adresse')->getData()) {
                $user->setAdresse($form->get('adresse')->getData());
            }
            if ($form->get('code_postal')->getData() && $form->get('code_postal')->getData()) {
                $user->setCodePostal($form->get('code_postal')->getData());
            }
            if ($form->get('region')->getData()) {
                $user->setRegion($form->get('region')->getData());
            }
            $user->setLastName($user->getLastName());

            $entityManager->persist($user);

            $entityManager->flush();

            return $this->redirectToRoute('home');
        }

        return $this->render('registration/registerCustomer.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    #[Route('/registration/cleaner', name: 'app_registration_cleaner')]
    public function registerCleaner(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager, FileUploader $fileUploader): Response
    {
        $user = new Cleaner();
        $form = $this->createForm(RegistrationCleanerFormType::class, $user);
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

            $student_proof = $form->get('student_proof')->getData();
            if ($student_proof) {
                $StudentProofFileName = $fileUploader->upload($student_proof);
                $user->setStudentProof($StudentProofFileName);
            }
            $user->setPhoneNumber($form->get('phoneNumber')->getData());
            $user->setRoles(["ROLE_CLEANER"]);
            $entityManager->persist($user);
            $entityManager->flush();
            return $this->redirectToRoute('home');
        }
        return $this->render('registration/registerCleaner.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
}
