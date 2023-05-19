<?php

namespace App\Form;

use App\Entity\Cleaner;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class RegistrationCleanerFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $isEditProfile = $options['is_edit_profile'] ?? false;

        $builder
            ->add('email')
            ->add('first_name', TextType::class)
            ->add('last_name', TextType::class)
            ->add('phoneNumber', TextType::class, [
                'attr' => [
                    'maxlength' => 15,
                ],
            ])
            ->add('image', FileType::class, [
                'required' => false,
                'mapped' => false,
                'data_class' => null,
                'attr' => ['accept' => 'image/*']
            ])
            ->add('studen_proof', FileType::class, [
            'required' => true,
            'mapped' => false,
            'data_class' => null,
            'attr' => ['accept' => 'image/*']
            ]);
        if ($isEditProfile) {
            $builder
                ->add('currentPassword', PasswordType::class, [
                    'mapped' => false,
                    'attr' => ['autocomplete' => 'current-password'],
                    'constraints' => [
                        new NotBlank([
                            'message' => 'Veuillez entrer le mot de passe actuel',
                        ]),
                    ],
                ])
                ->add('plainPassword', PasswordType::class, [
                    'mapped' => false,
                    'required' => false,
                    'constraints' => [
                        new Length([
                            'min' => 6,
                            'minMessage' => 'Votre mot de passe doit être d\'une longueur minimal de {{ limit }} charactères',
                            'max' => 4096,
                        ]),
                    ],
                ]);
        } else {
            $builder->add('plainPassword', PasswordType::class, [
                'mapped' => false,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez entré un mot de passe !',
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Votre mot de passe doit être d\'une longueur minimal de {{ limit }} caractères',
                        'max' => 4096,
                    ]),
                ],
            ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'is_edit_profile' => false,
        ]);
    }
}