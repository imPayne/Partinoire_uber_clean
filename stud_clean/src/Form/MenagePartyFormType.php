<?php

namespace App\Form;

use App\Entity\Housework;
use Symfony\Component\Form\AbstractType;
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
        $builder
            ->add('title', TextType::class)
            ->add('description', TextareaType::class)
            ->add('dateStart', DateTimeType::class, [
                'html5' => true,
                'widget' => 'single_text',
                'attr' => [
                    'min' => (new \DateTime('now'))->format('Y-m-d\TH:i'),
                    'max' => (new \DateTime('+12 months'))->format('Y-m-d\TH:i'),
                ],
            ])
            ->add('listImage', FileType::class, [
                'multiple' => false,
                'attr' => ['accept' => 'image/*'],
                'mapped' => false,
                'required' => false,
            ])
            ->add('Participant', ParticipantFormType::class)
        ;
    }
}
