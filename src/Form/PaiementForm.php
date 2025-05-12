<?php
// src/Form/PaiementForm.php

namespace App\Form;

use App\Entity\Paiement;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class PaiementForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('montant', MoneyType::class, [
                'currency' => 'EUR', // Monnaie (par exemple, EUR)
                'label' => 'Montant à payer',
            ])
            ->add('cardNumber', TextType::class, [
                'label' => 'Numéro de carte',
                'required' => true,
                'attr' => ['maxlength' => 19, 'placeholder' => '1234 5678 9012 3456'],
            ])
            ->add('cardType', ChoiceType::class, [
                'label' => 'Type de carte',
                'choices' => [
                    'Visa' => 'visa',
                    'MasterCard' => 'mastercard',
                    'American Express' => 'amex',
                ],
                'placeholder' => 'Sélectionnez un type de carte',
                'required' => true,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Paiement::class,
        ]);
    }
}
