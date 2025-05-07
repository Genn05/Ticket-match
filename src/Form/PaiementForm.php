<?php

namespace App\Form;

use App\Entity\Paiement;
use App\Entity\Ticket;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PaiementForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('datePaiement')
            ->add('montant')
            ->add('user', EntityType::class, [ // Assurez-vous que le nom du champ soit cohérent avec l'entité
                'class' => User::class,
                'choice_label' => 'id',
            ])
            ->add('ticket', EntityType::class, [
                'class' => Ticket::class, // Utilisez Ticket avec une majuscule
                'choice_label' => 'id',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Paiement::class,
        ]);
    }
}
