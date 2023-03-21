<?php

namespace App\Controller;

use App\Entity\Customer;
use App\Entity\Housework;
use App\Entity\Participant;
use App\Entity\Service;
use App\Repository\CustomerRepository;
use App\Repository\HouseworkRepository;
use App\Repository\ParticipantRepository;
use App\Repository\ServiceRepository;
use App\Service\FileUploader;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
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
    public function index(ServiceRepository $serviceRepository, FileUploader $fileUploader, Request $request, CustomerRepository $customerRepository, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        if (!$user) {
            return ($this->redirectToRoute('app_login'));
        }
        $newHousework = new Housework();

        $services = $serviceRepository->findAll();
        $serviceChoices = [];
        foreach ($services as $service) {
            $serviceChoices[$service->getName()] = $service->getName();
        }

        $form = $this->createFormBuilder()
            ->add('title', TextType::class)
            ->add('description', TextareaType::class)
            ->add('dateStart', DateTimeType::class,  [
                'date_widget' => 'single_text',
                'placeholder' => [
                    'year' => 'Year', 'month' => 'Month', 'day' => 'Day',
                    'hour' => 'Hour', 'minute' => 'Minute', 'second' => 'Second',
                ],
            ])
            ->add('listImage', FileType::class, [
                'multiple' => false,
                'attr' => ['accept' => 'image/*'],
                'mapped' => false,
                'required' => false,
            ])
            ->add('services', ChoiceType::class, [
                'label' => "Associé(s) un ou plusieurs services",
                'choices' => $serviceChoices,
                'multiple' => true,
                'expanded' => true,
            ])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $customer = $customerRepository->findOneBy(['email' => $user->getUserIdentifier()]);
            if ($customer) {
                $newHousework->setDateStart($form->get('dateStart')->getData()); // passe l'objet DateTime à setDateStart()
                $newHousework->setDescription($form->get('description')->getData());
                $newHousework->setTitle($form->get('title')->getData());
                $image = $form->get('listImage')->getData();
                $fileName = $fileUploader->upload($image);
                $newHousework->setListImage($fileName);

                foreach ($form->get('services')->getData() as $serviceChoosen) {
                    $newParticipant = new Participant();
                    $selectServiceFromDb = $serviceRepository->findOneBy(['name' => $serviceChoosen]);
                    $newParticipant->setService($selectServiceFromDb);
                    $newHousework->addParticipant($newParticipant);
                    $entityManager->persist($newParticipant);
                }
                $customer->addHousework($newHousework);
                $entityManager->persist($customer);
                $entityManager->persist($newHousework);
                $entityManager->flush();

                return $this->redirectToRoute("app_profile");
            }
        }

        return $this->render('housework/index.html.twig', [
            'controller_name' => 'HouseworkController',
            'form' => $form->createView(),
            'services' => $serviceChoices
        ]);
    }
    #[Route('/my_houseworks', name: 'app_show_houseworks')]
    public function myHouseworks(CustomerRepository $customerRepository, ParticipantRepository $participantRepository,  HouseworkRepository $houseworkRepository): Response
    {
        $user = $this->getUser();
        if (!$user) {
            return ($this->redirectToRoute('app_login'));
        }
        $customer = $customerRepository->findOneBy(['email' => $this->getUser()]);

        return $this->render('housework/myHouseworks.html.twig', [
            'controller_name' => 'HouseworkController',
            'customer' => $customer,
        ]);
    }
}
