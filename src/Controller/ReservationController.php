<?php

namespace App\Controller;

use App\Entity\Ticket;
use App\Entity\Reservation;
use App\Form\ReservationFormType;
use App\Form\ReservationFormTypeForm;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ReservationController extends AbstractController
{
    #[Route('/reservation/{id}', name: 'app_reservation')]
    public function reserver(
        Ticket $ticket,
        Request $request,
        EntityManagerInterface $em,
        Security $security
    ): Response {
        // Créer une nouvelle réservation
        $reservation = new Reservation();
        $reservation->setTicket($ticket);
        $reservation->setUser($security->getUser());
        $reservation->setCreatedAt(new \DateTimeImmutable());

        // Créer le formulaire
        $form = $this->createForm(ReservationFormTypeForm::class, $reservation, [
            'ticket_quantity' => $ticket->getQuantite(), // Passer la quantité de tickets disponibles au formulaire
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Récupérer la quantité demandée depuis le formulaire
            $quantiteDemande = $reservation->getQuantite();

            // Vérifier si la quantité demandée est disponible
            if ($quantiteDemande > $ticket->getQuantite()) {
                $this->addFlash('danger', 'Il n\'y a pas assez de tickets disponibles.');
                return $this->redirectToRoute('app_reservation', ['id' => $ticket->getId()]);
            }

            // Mettre à jour la quantité du ticket en fonction du nombre réservé
            $ticket->setQuantite($ticket->getQuantite() - $quantiteDemande);

            // Persist et flush la réservation et la mise à jour de la quantité de ticket
            $em->persist($reservation);
            $em->persist($ticket);  // Persist l'entité ticket pour sauvegarder la nouvelle quantité
            $em->flush();

            $this->addFlash('success', 'Réservation effectuée avec succès.');
            return $this->redirectToRoute('app_home');
        }

        return $this->render('reservation/index.html.twig', [
            'form' => $form->createView(),
            'ticket' => $ticket,
        ]);
    }
}
