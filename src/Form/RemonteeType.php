<?php


namespace App\Form;


use App\Dto\RemonteeFormData;
use App\Entity\Arrondissement;
use App\Entity\Departement;
use App\Entity\Resultat;
use App\Repository\DepartementRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Validator\Constraints\Positive;
use Symfony\Component\Validator\Constraints\PositiveOrZero;

class RemonteeType extends AbstractType
{
    private DepartementRepository $repository;

    /**
     * RemonteeType constructor.
     * @param DepartementRepository $repository
     */
    public function __construct(DepartementRepository $repository)
    {
        $this->repository = $repository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nomMandataire', TextType::class, [
                'label' => 'NOM et PRÉNOMS',
                'constraints' => [
                    new NotNull(),
                    new NotBlank(),
                ],
            ])
            ->add('telephoneMandataire', TelType::class, [
                'label' => 'Téléphone',
                'constraints' => [
                    new NotNull(),
                    new NotBlank(),
                ],
            ])
            ->add('nbVotants', IntegerType::class, [
                'label' => 'Nombre de votants',
                'attr' => [
                ],
                'constraints' => [
                    new NotNull(),
                    new NotBlank(),
                    new Positive(),
                ],
            ])
            ->add('nbVoixRlc', IntegerType::class, [
                'label' => 'RLC - Restaurer La Confiance',
                'attr' => [
                ],
                'constraints' => [
                    new NotNull(),
                    new NotBlank(),
                    new PositiveOrZero(),
                ],
            ])
            ->add('nbVoixFcbe', IntegerType::class, [
                'label' => 'FCBE - Forces Cauris pour un Bénin Emergent',
                'attr' => [
                ],
                'constraints' => [
                    new NotNull(),
                    new NotBlank(),
                    new PositiveOrZero(),
                ],
            ])
            ->add('nbVoixDuoTT', IntegerType::class, [
                'label' => 'TT - Talon.Talata',
                'attr' => [
                ],
                'constraints' => [
                    new NotNull(),
                    new NotBlank(),
                    new PositiveOrZero(),
                ],
            ])
            ->add('nbNuls', IntegerType::class, [
                'label' => 'Votes nuls',
                'attr' => [
                ],
                'constraints' => [
                    new NotNull(),
                    new NotBlank(),
                    new PositiveOrZero(),
                ],
            ])
            ->add('observations', TextareaType::class, [
                'label' => 'Observations éventuelles',
                'attr' => [
                ],
                'constraints' => [
                    new NotNull(),
                    new NotBlank(),
                ],
            ])
            ->add('departement', EntityType::class, [
                'label' => 'Votre département',
                'class' => Departement::class,
                'choices' => $this->repository->findBy([], ['nom' => 'ASC']),
                'mapped' => false,
                'required' => false,
                'attr' => [
                    'class' => 'form-select js-departement',
                ],
            ])
//            ->add('commune', ChoiceType::class, [
//                'label' => 'Choisissez la Commune',
//                'mapped' => false,
//                'required' => false,
//                'attr' => [
//                    'class' => 'form-select js-commune',
//                ],
//                ''
//            ])
//            ->add('arrondissement', ChoiceType::class, [
//                'label' => 'Choisissez l\'Arrondissement',
//                'mapped' => false,
//                'required' => false,
//                'attr' => [
//                    'class' => 'form-select js-arrondissement',
//                ],
//            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefault('data_class', Resultat::class);
        $resolver->setDefault('allow_extra_fields', true);
    }
}
