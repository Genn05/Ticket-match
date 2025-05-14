<?php

namespace App\Form;

use App\Entity\Card;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CardForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('cardNumber')
            ->add('balance', NumberType::class, [
                'label' => 'Balance',
                'required' => true,
                'scale' => 2, // Ensures two decimal places
            ])
            ->add('user', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'id',
            ])
            ->add('cardType', TextType::class, [
                'label' => 'Type de carte',
                'mapped' => false, // Prevents Symfony from looking for a "cardType" property in the entity
                'required' => false,
            ])
            ->add('card', EntityType::class, [
                'class' => Card::class,
                'choice_label' => 'cardNumber', // Display the card number in the dropdown
                'label' => 'Select Card',
                'required' => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Card::class,
        ]);
    }
}
