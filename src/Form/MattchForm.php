<?php

namespace App\Form;

use App\Entity\Mattch;
use App\Entity\Stade;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class MattchForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('equipeA')
            ->add('equipeB')
            ->add('dateMatch')
            ->add('stade', EntityType::class, [
                'class' => Stade::class,
                'choice_label' => 'nom',
            ])
            ->add('imageEquipeDomicile', FileType::class, [
                'label' => 'Logo Équipe Domicile',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '2M',
                        'mimeTypes' => ['image/jpeg', 'image/png'],
                        'mimeTypesMessage' => 'Veuillez uploader une image valide (JPEG/PNG)',
                    ]),
                ],
            ])
            ->add('imageEquipeExterieur', FileType::class, [
                'label' => 'Logo Équipe Extérieure',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '2M',
                        'mimeTypes' => ['image/jpeg', 'image/png'],
                        'mimeTypesMessage' => 'Veuillez uploader une image valide (JPEG/PNG)',
                    ]),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Mattch::class,
        ]);
    }
}
