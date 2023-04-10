<?php

namespace App\Form;

use App\Entity\Housework;
use App\Entity\Participant;
use App\Entity\Service;
use App\Repository\ServiceRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;
use Symfony\Component\Validator\Constraints\LessThanOrEqual;


class MenagePartyFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $minDate = new \DateTime('+2 days');
        $maxDate = new \DateTime('+2 month');

        $builder
            ->add('title', TextType::class)
            ->add('description', TextareaType::class)
            ->add('dateStart', DateType::class, [
                'widget' => 'single_text',
                'html5' => false,
                'constraints' => [
                    new GreaterThanOrEqual($minDate),
                    new LessThanOrEqual($maxDate),
                ],
            ])
            ->add('listImage', FileType::class, [
                'multiple' => false,
                'attr' => ['accept' => 'image/*'],
                'mapped' => false,
                'required' => false,
            ])
            ->add('services', EntityType::class, [
                'label' => "Associé(s) un ou plusieurs services",
                'class' => Service::class,
                'choice_label' => 'name',
                'multiple' => true,
                'expanded' => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Housework::class,
        ]);
    }
}
