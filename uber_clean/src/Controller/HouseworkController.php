<?php

namespace App\Controller;

use App\Entity\Customer;
use App\Entity\Housework;
use App\Repository\CustomerRepository;
use App\Repository\HouseworkRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class HouseworkController extends AbstractController
{
    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    #[Route('/housework', name: 'app_housework')]
    public function index(SessionInterface $session, Request $request, CustomerRepository $customerRepository, EntityManagerInterface $entityManager): Response
    {
        $this->addFlash('debug', 'La méthode index() du HouseworkController est appelée.');

        $user = $this->getUser();
        if (!$user) {
            return ($this->redirectToRoute('home'));
        }
        if ($request->isMethod('POST') && $user) {
            $customer = $customerRepository->findOneBy(['email' => $user->getUserIdentifier()]);
            if ($customer) {
                $newHousework = new Housework();
                $currentDate = date('Y-m-d H:i:s'); // récupère la date et l'heure actuelles
                $dateObject = DateTime::createFromFormat('Y-m-d H:i:s', $currentDate); // convertit en objet DateTime
                $newHousework->setDateStart($dateObject); // passe l'objet DateTime à setDateStart()
                $customer->addHousework($newHousework);
                $session->getFlashBag()->add('success', 'Votre Ménage Party à bien été créer');
                //$entityManager->persist($customer);
                $entityManager->persist($newHousework);
                $entityManager->flush();
            }
        }


        return $this->render('housework/index.html.twig', [
            'controller_name' => 'HouseworkController',
        ]);
    }
}
