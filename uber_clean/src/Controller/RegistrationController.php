<?php

namespace App\Controller;

use App\Entity\Cleaner;
use App\Entity\Customer;
use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Service\FileUploader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager, FileUploader $fileUploader): Response
    {
        $user = new User();
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

            $user->setRoles(["ROLE_USER", "ROLE_CLEANER", "ROLE_CUSTOMER"]);

            $customer = new Customer();
            $cleaner = new Cleaner();
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

            return $this->redirectToRoute('home');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
}
