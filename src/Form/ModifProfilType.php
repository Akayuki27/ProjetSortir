<?php

namespace App\Form;

use App\Entity\Campus;
use App\Entity\Participant;
use App\Entity\Sortie;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ModifProfilType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('pseudo', TextType::class,  [
                'attr' => [
                    'placeholder' => $options['pseudo'] ?: 'Entrez votre pseudo',
                ],
            ])
            ->add('roles')
            ->add('password')
            ->add('lastname')
            ->add('firstname')
            ->add('phoneNumber')
            ->add('email')
            ->add('active')
            ->add('estInscrit', EntityType::class, [
                'class' => Sortie::class,
'choice_label' => 'id',
'multiple' => true,
            ])
            ->add('estRattacheA', EntityType::class, [
                'class' => Campus::class,
'choice_label' => 'id',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Participant::class,
            'pseudo' => null,
        ]);
    }
}
