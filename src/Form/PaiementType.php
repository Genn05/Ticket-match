<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Entity\Paiement;

class PaiementType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('cardType', TextType::class, [
            'label' => 'Type de carte',
            'mapped' => false, // Prevents Symfony from looking for a "cardType" property in the entity
            'required' => false,
        ]);

        $builder->add('montant', TextType::class, [
            'label' => 'Montant',
            'required' => true,
        ]);

        $builder->add('cardNumber', TextType::class, [
            'label' => 'Numéro de carte',
            'required' => true,
            'attr' => ['maxlength' => 19, 'placeholder' => '1234 5678 9012 3456'],
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Paiement::class,
        ]);
    }
}