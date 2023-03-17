<?php

namespace App\Controller;

use App\Repository\CustomerRepository;
use App\Repository\HouseworkRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Service;

class MenagePartyController extends AbstractController
{
    #[Route('/menage/party', name: 'app_menage_party')]
    public function index(HouseworkRepository $houseworkRepository, SessionInterface $session, Request $request, CustomerRepository $customerRepository): Response
    {
        //$menagePartys = $houseworkRepository->findAll();

        $customers = $customerRepository->findAll();

        return $this->render('menage_party/index.html.twig', [
            'controller_name' => 'MenagePartyController',
            "customers" => $customers,
        ]);
    }
}
