<?php

namespace App\Controller;

use App\Form\LoginFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class LoginController extends AbstractController
{
    #[Route('/login', name: 'app_login')]
    public function index(Request $request, AuthenticationUtils $authenticationUtils): Response
    {
        $form = $this->createForm(loginFormType::class);


        // Récupérer les erreurs du formulaire s'il y a des erreurs
        $error = $authenticationUtils->getLastAuthenticationError();

        // Récupérer le dernier email saisi
        $lastEmail = $authenticationUtils->getLastUsername();

        return $this->render('login/index.html.twig', [
            'error' => $error,
            'last_username' => $lastEmail,
            'form' => $form->createView(),
        ]);
    }
}
