<?php

namespace App\Controller;

use App\Entity\Cleaner;
use App\Entity\Customer;
use App\Entity\Housework;
use App\Entity\Participant;
use App\Form\DeleteHouseworkFormType;
use App\Form\RegisterCleanerToHouseworkType;
use App\Repository\CleanerRepository;
use App\Repository\CustomerRepository;
use App\Repository\HouseworkRepository;
use App\Repository\ParticipantRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Attribute\IsGranted;

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

    #[Route('/menage_party/{id}', name: 'app_register_cleaner_to_housework', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function registerCleanerToHousework(Housework $housework, HouseworkRepository $houseworkRepository, CustomerRepository $customerRepository, ParticipantRepository $participantRepository, CleanerRepository $cleanerRepository, Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();

        $form = $this->createForm(RegisterCleanerToHouseworkType::class);
        $form->handleRequest($request);

        if (!($user instanceof Cleaner) && !($user instanceof Customer)) {
            $this->redirectToRoute('app_login');
        }
        $id = $request->get('id');
        $cleaner = $cleanerRepository->findOneBy(['email' => $user->getEmail()]);
        $customer = $customerRepository->findOneBy(['email' => $user->getEmail()]);

        $newCleanerParticipant = $participantRepository->findBy(['housework' => $housework]);
        $getHousework = $houseworkRepository->find($id);

        $deleteForm = $this->createForm(DeleteHouseworkFormType::class);
        $deleteForm->handleRequest($request);

        if ($cleaner) {
            if ($form->isSubmitted() && $form->isValid()) {
                if ($form->get('submit')->isClicked()) {
                    foreach ($newCleanerParticipant as $participant) {
                        $participant->setCleaner($cleaner);
                        $housework->addParticipant($participant);
                        $entityManager->persist($participant);
                        $entityManager->flush();
                    }

                    return $this->redirectToRoute('app_register_cleaner_to_housework', ['id' => $housework->getId()]);
                }
            }
        }
        else if ($customer && $getHousework) {
            if ($deleteForm->isSubmitted() && $deleteForm->isValid()) {
                $entityManager->remove($housework);
                $entityManager->flush();
            }
        }

        return $this->render('menage_party/Detail.html.twig', [
            'housework' => $housework,
            'form' => $form->createView(),
            'deleteForm' => $deleteForm->createView(),
            'cleaner' => $cleaner,
            'listParticipantHousework' => $newCleanerParticipant,
        ]);
    }


}