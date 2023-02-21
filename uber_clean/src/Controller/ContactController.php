<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ContactController extends AbstractController
{
    #[Route('/contact', name: 'contact')]
    public function contact(Request $request): Response
    {
        $nom = '';
        $email = '';
        $message = '';
        $errors = [];
        $success = false;

        if ($request->isMethod('POST')) {
            $nom = $request->request->get('nom');
            $email = $request->request->get('email');
            $message = $request->request->get('message');

            if (empty($nom)) {
                $errors[] = 'Le nom est obligatoire.';
            }

            if (empty($email)) {
                $errors[] = 'L\'adresse e-mail est obligatoire.';
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = 'L\'adresse e-mail n\'est pas valide.';
            }

            if (empty($message)) {
                $errors[] = 'Le message est obligatoire.';
            }

            if (empty($errors)) {
                // Envoyer le message
                $success = true;
            }
        }

        return $this->render('contact/index.html.twig', [
            'nom' => $nom,
            'email' => $email,
            'message' => $message,
            'errors' => $errors,
            'success' => $success,
        ]);
    }
}
