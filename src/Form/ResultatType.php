<?php

namespace App\Form;

use App\Entity\Arrondissement;
use App\Entity\Resultat;
use App\Repository\ArrondissementRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\RadioType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Validator\Constraints\Positive;
use Symfony\Component\Validator\Constraints\PositiveOrZero;

class ResultatType extends AbstractType
{
    private ArrondissementRepository $repository;

    /**
     * ResultatType constructor.
     * @param ArrondissementRepository $repository
     */
    public function __construct(ArrondissementRepository $repository)
    {
        $this->repository = $repository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('arrondissement', EntityType::class, [
                'label' => 'Arrondissement',
                'class' => Arrondissement::class,
                'choices' => $this->repository->findBy([], ['nom' => 'ASC']),
                'attr' => [
                    'class' => 'form-select mb-3',
                ],
            ])
            ->add('nomMandataire', TextType::class, [
                'label' => 'Nom et prénoms du mandataire',
                'constraints' => [
                    new NotNull(),
                    new NotBlank(),
                ],
                'attr' => [
                    'class' => 'mb-3',
                ],
            ])
            ->add('telephoneMandataire', TextType::class, [
                'label' => 'Numéro de téléphone du mandataire',
                'constraints' => [
                    new NotNull(),
                    new NotBlank(),
                ],
                'attr' => [
                    'class' => 'mb-3',
                ],
            ])
            ->add('nbVotants', IntegerType::class, [
                'label' => 'Nombre de votants',
                'constraints' => [
                    new NotNull(),
                    new NotBlank(),
                    new Positive(),
                ],
                'attr' => [
                    'class' => 'mb-3',
                ],
            ])
            ->add('nbVoixRlc', IntegerType::class, [
                'label' => 'RLC - KOHOUE & AGOSSA',
                'constraints' => [
                    new NotNull(),
                    new NotBlank(),
                    new PositiveOrZero(),
                ],
                'attr' => [
                    'class' => 'mb-3',
                ],
            ])
            ->add('nbVoixFcbe', IntegerType::class, [
                'label' => 'FCBE - DJIMBA & HOUNKPE',
                'constraints' => [
                    new NotNull(),
                    new NotBlank(),
                    new PositiveOrZero(),
                ],
                'attr' => [
                    'class' => 'mb-3',
                ],
            ])
            ->add('nbVoixDuoTT', IntegerType::class, [
                'label' => 'TT - TALON & TALATA',
                'constraints' => [
                    new NotNull(),
                    new NotBlank(),
                    new PositiveOrZero(),
                ],
                'attr' => [
                    'class' => 'mb-3',
                ],
            ])
            ->add('nbNuls', IntegerType::class, [
                'label' => 'Bulletins nuls',
                'constraints' => [
                    new NotNull(),
                    new NotBlank(),
                    new PositiveOrZero(),
                ],
                'attr' => [
                    'class' => 'mb-3',
                ],
            ])
            ->add('observations', TextareaType::class, [
                'label' => 'Observations',
                'constraints' => [
                    new NotNull(),
                    new NotBlank(),
                ],
                'attr' => [
                    'class' => 'mb-3',
                ],
            ])
            ->add('warningFlag', RadioType::class, [
                'label' => 'Flag',
                'required' => false,
                'attr' => [
                    'class' => 'mb-3',
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
