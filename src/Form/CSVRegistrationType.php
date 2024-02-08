<?php

namespace App\Form;

use App\Entity\Campus;
use App\Entity\Sortie;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\NotBlank;

class CSVRegistrationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('csv', FileType::class, [
                'label' => 'Fichier CSV',
                'constraints' => [
                    new File([
                        'mimeTypes' => [
                            'text/csv',
                            'text/plain',
                        ],
                        'mimeTypesMessage' => 'Veuillez télécharger un fichier valide - csv ou txt.',
                    ]),
                ],
            ])
            ->add('estRattacheA', EntityType::class,[
                    'class' => Campus::class,
                    'choice_label' => 'nom',
                ]
            )
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([

        ]);
    }
}
