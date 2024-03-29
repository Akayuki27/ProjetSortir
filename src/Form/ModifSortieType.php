<?php

namespace App\Form;

use App\Entity\Campus;
use App\Entity\Etat;
use App\Entity\Lieu;
use App\Entity\Participant;
use App\Entity\Sortie;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ModifSortieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('dateHeureDebut')
            ->add('duree')
            ->add('dateLimiteInscription')
            ->add('nbInscriptionMax')
            ->add('infosSortie', TextareaType::class, [
                'attr' => [
                    'rows' => 4, //
                    'placeholder' => 'Votre desciption',
                ],
            ])
            //->add('participants', EntityType::class, [
              //  'class' => Participant::class,
//'choice_label' => 'id',
//'multiple' => true,
  //          ])

            ->add('etat', EntityType::class, [
                'class' => Etat::class,
'choice_label' => 'libelle',
            ])
            ->add('campus', EntityType::class, [
                'class' => Campus::class,
                'choice_label' => 'nom',
            ])
            ->add('Lieu', EntityType::class, [
                'class' => Lieu::class,
'choice_label' => 'nom',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Sortie::class,
        ]);
    }
}
