<?php

namespace App\Controller;

use App\Entity\Service;
use App\Form\ServiceType;
use App\Repository\ServiceRepository;
use App\Service\FileUploader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class EventsController extends AbstractController
{
    #[Route('/events', name: 'app_events')]
    public function index(ServiceRepository $serviceRepository): Response
    {
        $servicesList = $serviceRepository->findAll();

        return $this->render('events/index.html.twig', [
            'controller_name' => 'EventsController',
            'services' => $servicesList,
        ]);
    }

    #[Route('/create_event', name: 'app_create_event')]
    public function createEvent(EntityManagerInterface $entityManager, Request $request, ServiceRepository $serviceRepository, FileUploader $fileUploader): Response
    {
        $user = $this->getUser();

        if (!($user instanceof PasswordAuthenticatedUserInterface)) {
            return $this->redirectToRoute('app_login');
        }
        $service = new Service();

        $form = $this->createForm(ServiceType::class, $service);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $service->setName($form->get('name')->getData());

            $image = $form->get('icon')->getData();
            if ($image) {
                $fileName = $fileUploader->upload($image);
                $service->setIcon($fileName);
            }
            $entityManager->persist($service);
            $entityManager->flush();
            return $this->redirectToRoute('app_events');
        }

        return $this->render('events/createEvent.html.twig', [
            'controller_name' => 'EventsController',
            'form' => $form->createView(),
        ]);
    }
}
