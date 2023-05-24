<?php

namespace App\Controller;

use App\Entity\Cleaner;
use App\Entity\Customer;
use App\Entity\Housework;
use App\Entity\Participant;
use App\Form\DeleteHouseworkFormType;
use App\Form\EditMenagePartyFormType;
use App\Form\MenagePartyFormType;
use App\Service\FileUploader;
use Symfony\Component\VarDumper\VarDumper;
use App\Form\RegisterCleanerToHouseworkType;
use App\Form\UnsubscribeCleanFromHouseworkType;
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

        $newCleanerParticipant = $participantRepository->findOneBy(['housework' => $housework]);
        if ($newCleanerParticipant && $newCleanerParticipant->getCleaner()) {
            $getCleanerParticipant = $cleanerRepository->findBy(['id' => $newCleanerParticipant->getCleaner()]);
        }


        $getHousework = $houseworkRepository->find($id);

        $deleteForm = $this->createForm(DeleteHouseworkFormType::class);
        $deleteForm->handleRequest($request);

        $unsubscribeForm = $this->createForm(UnsubscribeCleanFromHouseworkType::class);
        $unsubscribeForm->handleRequest($request);

        $editFormButton = $this->createForm(EditMenagePartyFormType::class);
        $editFormButton->handleRequest($request);

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
            if ($unsubscribeForm->isSubmitted() && $unsubscribeForm->isValid()) {
                if ($unsubscribeForm->get('submit')->isClicked()) {
                    foreach ($newCleanerParticipant as $participant) {
                        $participant->setCleaner(null);
                        $entityManager->persist($participant);
                        $entityManager->flush();
                    }

                    return $this->redirectToRoute('app_upcoming_performances');
                }
            }
        }
        else if ($customer && $getHousework) {
            if ($deleteForm->isSubmitted() && $deleteForm->isValid()) {
                $entityManager->remove($housework);
                $entityManager->flush();
                return $this->redirectToRoute('app_show_houseworks');
            }

            else if ($editFormButton->isSubmitted() && $editFormButton->isValid()) {
                return $this->redirectToRoute('app_edit_housework', ['id' => $housework->getId()]);            }
        }

        return $this->render('menage_party/Detail.html.twig', [
            'housework' => $housework,
            'form' => $form->createView(),
            'deleteForm' => $deleteForm->createView(),
            'unsubscribeForm' => $unsubscribeForm->createView(),
            'editFormButton' => $editFormButton->createView(),
            'cleaner' => $cleaner,
            'listParticipantHousework' => $newCleanerParticipant,
        ]);
    }
    #[Route('/menage_party/edit/{id}', name: 'app_edit_housework', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_CUSTOMER')] //Changer par ROLE_USER si jamais ca ne fonctionne pas
    public function editHousework(FileUploader $fileUploader, Housework $housework, HouseworkRepository $houseworkRepository, CustomerRepository $customerRepository, ParticipantRepository $participantRepository, CleanerRepository $cleanerRepository, Request $request, EntityManagerInterface $entityManager): Response
    {
        $idMenageParty = $request->get('id');
        $editHousework = $houseworkRepository->find($idMenageParty);
        $customer = $customerRepository->findOneBy(['email' => $this->getUser()->getUserIdentifier()]);

        $editForm = $this->createForm(MenagePartyFormType::class);

        $editForm->get('title')->setData($editHousework->getTitle());
        $editForm->get('description')->setData($editHousework->getDescription());
        $editForm->get('dateStart')->setData($editHousework->getDateStart());
        $hourIntoString = $editHousework->getHour()->format('H:i');
        $editForm->get('hours')->setData($hourIntoString);
        $participantForm = $editForm->get('Participant');

        $editForm->handleRequest($request);
        if ($editForm->isSubmitted() && $editForm->isValid()) {
            // Modifier les données de l'entité Housework à partir des valeurs du formulaire
            if ($editForm->get('title')->getData() !== $editHousework->getTitle()) {
                $editHousework->setTitle($editForm->get('title')->getData());
            }
            if ($editForm->get('description')->getData() !== $editHousework->getDescription()) {
                $editHousework->setDescription($editForm->get('description')->getData());
            }
            if ($editForm->get('dateStart')->getData() !== $editHousework->getDateStart()) {
                $editHousework->setDateStart($editForm->get('dateStart')->getData());
            }
            if ($editForm->get('hours')->getData() !== $hourIntoString) {
                $newHours = $editForm->get('hours')->getData();
                $editHousework->setHour(new \DateTime($newHours));
            }

            if ($editForm->get('price')->getData() !== $editHousework->getPrice()) {
                $editHousework->setPrice($editForm->get('price')->getData());
            }
            if ($editForm->get('listImage')->getData()) {
                $image = $editForm->get('listImage')->getData();
                $fileName = $fileUploader->upload($image);
                $editHousework->setListImage($fileName);
            }
            if ($participantForm->get('service')->getData()) {
                // Modifier les données de l'entité Participant si un service a été choisi dans le formulaire
                $newParticipant = $participantRepository->findOneBy(['housework' => $editHousework]);
                if ($newParticipant) {
                    $newParticipant->setService($participantForm->get('service')->getData());
                    $newParticipant->setHousework($editHousework);
                    $entityManager->persist($newParticipant);
                }
            }
            $entityManager->flush();
            return $this->redirectToRoute('app_menage_party', ['id' => $housework->getId()]);
        }

        return $this->render('menage_party/edit.html.twig' , [
            'editForm' => $editForm->createView(),
            'houseworkToEdit' => $editHousework,
            'customer' => $customer,
        ]);
    }
}