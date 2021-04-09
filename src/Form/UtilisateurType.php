<?php

namespace App\Form;

use App\Entity\Utilisateur;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UtilisateurType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => 'Nom',
                'required' => false,
                'attr' => [
                    'class' => 'mb-3',
                ]
            ])
            ->add('prenoms', TextType::class, [
                'label' => 'Prénom (s)',
                'required' => false,
                'attr' => [
                    'class' => 'mb-3',
                ]
            ])
            ->add('email', EmailType::class, [
                'label' => 'Adresse mail',
                'attr' => [
                    'class' => 'mb-3',
                ]
            ])
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'required' => false,
                'mapped' => false,
                'first_options' => [
                    'label' => 'Mot de passe',
                    'attr' => [
                        'class' => 'mb-3',
                    ]
                ],
                'second_options' => [
                    'label' => 'Confirmer mot de passe',
                    'attr' => [
                        'class' => 'mb-3',
                    ]
                ],
            ])
            ->add('roles', ChoiceType::class, [
                'label' => 'Rôles',
                'attr' => [
                    'class' => 'mb-3',
                ],
                'required' => false,
                'multiple' => true,
                'expanded' => true,
                'choices' => [
//                    'ROLE_USER' => 'ROLE_USER',
                    'ROLE_DASHBOARD' => 'ROLE_DASHBOARD',
                    'ROLE_SUPERVISEUR' => 'ROLE_SUPERVISEUR',
                    'ROLE_RESPONSABLE' => 'ROLE_RESPONSABLE',
                    'ROLE_ADMIN' => 'ROLE_ADMIN',
                    'ROLE_SUPER_ADMIN' => 'ROLE_SUPER_ADMIN',
                ],
            ])
//            ->add('password')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Utilisateur::class,
        ]);
    }
}
