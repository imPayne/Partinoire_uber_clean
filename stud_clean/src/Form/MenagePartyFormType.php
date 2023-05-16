<?php

namespace App\Form;

use App\Entity\Housework;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;
use Symfony\Component\Validator\Constraints\LessThanOrEqual;


class MenagePartyFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $hours = range(8, 19); // Tableau des heures de 8h à 20h
        $minutes = ['00', '15', '30', '45']; // Tableau des minutes

        $choices = [];

        foreach ($hours as $heure) {
            foreach ($minutes as $minute) {
                $choices[sprintf('%02d:%02d', $heure, $minute)] = sprintf('%02d:%02d', $heure, $minute);
            }
        }

        $builder
            ->add('title', TextType::class)
            ->add('description', TextareaType::class)
            ->add('dateStart', DateType::class, [
                'html5' => true,
                'widget' => 'single_text',
                'attr' => [
                    'min' => (new \DateTime('now'))->format('Y-m-d'),
                    'max' => (new \DateTime('+12 months'))->format('Y-m-d'),
                ],
            ])

            ->add('hours', ChoiceType::class, [
                'choices' => $choices,
            ])
            ->add('listImage', FileType::class, [
                'multiple' => false,
                'attr' => ['accept' => 'image/*'],
                'mapped' => false,
                'required' => false,
                'data_class' => null,
            ])
            ->add('Participant', ParticipantFormType::class)
        ;
    }
}
