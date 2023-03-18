<?php

namespace App\Controller;

use App\Repository\HouseworkRepository;
use App\Repository\ServiceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MenagePartyController extends AbstractController
{
    #[Route('/menage/party', name: 'app_menage_party')]
    public function index(HouseworkRepository $houseworkRepository): Response
    {
        $houseworks = $houseworkRepository->findAll();

        return $this->render('menage_party/index.html.twig', [
            'controller_name' => 'MenagePartyController',
            'houseworks' => $houseworks,
        ]);
    }
}