<?php

namespace App\Form;

use App\Entity\Campus;
use App\Entity\Etat;
use App\Entity\Lieu;
use App\Entity\Participant;
use App\Entity\Sortie;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SortieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom')
            ->add('dateHeureDebut')
            ->add('duree')
            ->add('dateLimiteInscription')
            ->add('nbInscriptionMax')
            ->add('infosSortie')


            ->add('campus', EntityType::class, [
                'class' => Campus::class,
'choice_label' => 'nom',
            ])
            ->add('etat', EntityType::class, [
                'class' => Etat::class,
'choice_label' => 'libelle',
            ])
            ->add('Lieu', ChoiceType::class, [
                'choices' => [
                    'Sélectionner un lieu existant' => null,
                    'Nouveau lieu' => 'new', //créer un nouveau lieu ??? c'est con
                ],
                'mapped' => false, //pour indiquer à Symfony de ne pas mapper
                // ces champs à des propriétés de votre entité ?? est ce bien ca ?
            ])
            ->add('newLieu', TextType::class, [
                'required' => false,
                'mapped' => false, //pareil ???
            ]);

/*
            ->add('Lieu', EntityType::class, [
                'class' => Lieu::class,
'choice_label' => 'nom',
            ])
*/
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Sortie::class,
        ]);
    }
}
