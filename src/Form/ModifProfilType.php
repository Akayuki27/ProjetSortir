<?php

namespace App\Form;

use App\Entity\Campus;
use App\Entity\Participant;
use App\Entity\Sortie;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\Image;

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
            ->add('lastname')
            ->add('firstname')
            ->add('phoneNumber')
            ->add('email')
            ->add('estRattacheA', EntityType::class, [
                'class' => Campus::class,
'choice_label' => 'nom',
            ])
            ->add('isActive', CheckboxType::class, [
                'label' => 'Actif',
                'required' => false,
            ])
            ->add('imgName', FileType::class, [
                'label' => 'Image (JPG, JPEG)',
                'mapped' => false,
                'required' => false,
                'data_class'=> null, // Pour éviter les erreurs liées à la validation des données
                'constraints' => [
                    new File([
                        'maxSize' => '5M',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/pjpeg', // Certains navigateurs envoient des images JPEG avec ce type MIME
                            'image/png',
                        ],
                        'mimeTypesMessage' => 'Veuillez télécharger une image JPG ou JPEG valide',
                    ]),
                ],
            ]);
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
