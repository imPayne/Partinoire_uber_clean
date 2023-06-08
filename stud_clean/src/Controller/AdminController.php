<?php

namespace App\Controller;

use App\Form\AdminButtonVerifiedSubmitType;
use App\Repository\CleanerRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{
    #[Route('/admin', name: 'app_admin')]
    public function index(CleanerRepository $cleanerRepository, Request $request, EntityManagerInterface $entityManager): Response
    {
        $getAllCleanerDatas = $cleanerRepository->findAll();

        return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminController',
            'getAllCleanerDatas' => $getAllCleanerDatas,
        ]);
    }
    #[Route('/admin/to_verified/{id}', name: 'app_admin_verified')]
    public function verified(CleanerRepository $cleanerRepository, Request $request, EntityManagerInterface $entityManager): Response
    {
        $id = $request->get('id');
        $cleanerToVerified = $cleanerRepository->findOneBy(['id' => $id]);

        $formVerified = $this->createForm(AdminButtonVerifiedSubmitType::class);
        $formVerified->handleRequest($request);

        if ($formVerified->isSubmitted() && $formVerified->isValid()) {
            $cleanerToVerified->setChecked(true);
            $entityManager->persist($cleanerToVerified);
            $entityManager->flush();
            return $this->redirectToRoute("app_admin");
        }

        return $this->render('admin/accountToVerified.html.twig', [
            'button_verified' => $formVerified->createView(),
            'cleaner' => $cleanerToVerified,
            'controller_name' => 'AdminController',
            'cleanerToVerified' => $cleanerToVerified,
        ]);
    }
}
