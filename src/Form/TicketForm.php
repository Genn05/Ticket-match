<?php

namespace App\Form;

use App\Entity\Mattch;
use App\Entity\Ticket;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TicketForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('prix', MoneyType::class, [
                'currency' => 'EUR',
                'label' => 'Prix (€)',
            ])
            ->add('type', ChoiceType::class, [
                'choices' => [
                    'VIP' => 'VIP',
                    'Standard' => 'Standard',
                    'Étudiant' => 'Etudiant',
                ],
                'label' => 'Type de ticket',
                'placeholder' => 'Choisir un type',
            ])
            ->add('quantite', IntegerType::class, [
                'label' => 'Quantité disponible',
            ])
            ->add('Mattch', EntityType::class, [
                'class' => Mattch::class,
                'choice_label' => 'id', // ou 'nom' si ton entité Mattch a un champ nom
                'label' => 'Match associé',
                'placeholder' => 'Sélectionner un match',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Ticket::class,
        ]);
    }
}
