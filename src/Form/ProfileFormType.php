<?php
// src/Form/ProfileFormType.php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProfileFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        // Adding fields to the form
        $builder
            ->add('email', EmailType::class, [
                'label' => 'Email Address',
            ])
            ->add('plainPassword', PasswordType::class, [
                'label' => 'New Password',
                'required' => false, // Allowing empty passwords if the user doesn't want to change
                'mapped' => false,   // Don't map this directly to the User entity
                'attr' => [
                    'placeholder' => 'Leave empty to keep the current password'
                ]
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Update Profile'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        // The form will use the User entity, so make sure to set that as the data class
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
