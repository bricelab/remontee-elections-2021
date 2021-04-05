<?php


namespace App\Form;


use App\Dto\RemonteeFormData;
use App\Entity\Arrondissement;
use App\Entity\Departement;
use App\Entity\Resultat;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RemonteeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nomMandataire', TextType::class, [
                'label' => 'Nom'
            ])
            ->add('prenomsMandataire', TextType::class, [
                'label' => 'Prénom (s)'
            ])
            ->add('telephoneMandataire', TelType::class, [
                'label' => 'Numéro de Téléphone'
            ])
            ->add('nbVotants', IntegerType::class, [
                'label' => 'Nombre de votants',
                'attr' => [
                ],
            ])
            ->add('nbVoixRlc', IntegerType::class, [
                'label' => 'RLC - Restaurer La Confiance',
                'attr' => [
                ],
            ])
            ->add('nbVoixFcbe', IntegerType::class, [
                'label' => 'FCBE - Forces Cauris pour un Bénin Emergent',
                'attr' => [
                ],
            ])
            ->add('nbVoixDuoTT', IntegerType::class, [
                'label' => 'TT - Talon.Talata',
                'attr' => [
                ],
            ])
            ->add('nbNuls', IntegerType::class, [
                'label' => 'Votes nuls',
                'attr' => [
                ],
            ])
            ->add('observations', TextareaType::class, [
                'label' => 'Observations éventuelles',
                'attr' => [
                ],
            ])
            ->add('departement', EntityType::class, [
                'label' => 'Département',
                'class' => Departement::class,
                'mapped' => false,
                'required' => false,
                'attr' => [
                    'class' => 'form-select js-departement',
                ],
            ])
//            ->add('arrondissement', EntityType::class, [
//                'label' => 'Arrondissement',
//                'class' => Arrondissement::class,
//            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefault('data_class', Resultat::class);
        $resolver->setDefault('allow_extra_fields', true);
    }
}
