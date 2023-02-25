<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

class ProfileController extends AbstractController
{
    #[Route('/profile', name: 'app_profile')]
    public function profile(UserRepository $userRepository, UserInterface $user): Response
    {
        $currentUser = $userRepository->findOneBy(['email' => $user->getUserIdentifier()]);
        return $this->render('profile/index.html.twig', [
            'controller_name' => 'ProfileController',
            'current_user' => $currentUser,
        ]);
    }
}
