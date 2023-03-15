<?php

namespace App\Controller;

use App\Repository\ServiceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

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
}
