<?php

namespace App\Form;

use App\Entity\Arrondissement;
use App\Entity\Resultat;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ResultatType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nomMandataire')
            ->add('telephoneMandataire')
            ->add('nbVotants')
            ->add('nbVoixRlc')
            ->add('nbVoixFcbe')
            ->add('nbVoixDuoTT')
            ->add('nbNuls')
            ->add('observations')
            ->add('arrondissement', EntityType::class, [
                'label' => 'Arrondissement',
                'class' => Arrondissement::class,
                'attr' => [
                    'class' => 'form-select',
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Resultat::class,
        ]);
    }
}
