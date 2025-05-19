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
        Security $security,
        \App\Repository\ReservationRepository $reservationRepository
    ): Response {
        // Créer une nouvelle réservation
        $reservation = new Reservation();
        $reservation->setTicket($ticket);
        $reservation->setUser($security->getUser());
        $reservation->setCreatedAt(new \DateTimeImmutable());
        $reservation->setQuantite(1); // ✅ valeur par défaut

        // Créer le formulaire
           $form = $this->createForm(ReservationFormTypeForm::class, $reservation, [
    'ticket_quantity' => $ticket->getQuantite(),
 // Passer la quantité de tickets disponibles au formulaire
           ]);

        $form->handleRequest($request);

        $place = $request->query->get('place');
        if ($form->isSubmitted() && $form->isValid()) {
            $quantiteDemande = $reservation->getQuantite();
            if ($quantiteDemande > $ticket->getQuantite()) {
                $this->addFlash('danger', 'Il n\'y a pas assez de tickets disponibles.');
                return $this->redirectToRoute('app_reservation', ['id' => $ticket->getId()]);
            }
            $ticket->setQuantite($ticket->getQuantite() - $quantiteDemande);
            // Enregistrer la place sélectionnée dans la réservation
            if ($place) {
                $reservation->setPlace($place);
            }
            $em->persist($reservation);
            $em->persist($ticket);
            $em->flush();
            $this->addFlash('success', 'Réservation effectuée avec succès.');
            return $this->redirectToRoute('app_home');
        }

        // Récupérer les réservations de l'utilisateur connecté
        $user = $this->getUser();
        $reservations = [];
        if ($user) {
            $reservations = $reservationRepository->findBy(['user' => $user]);
        }

        return $this->render('reservation/index.html.twig', [
            'form' => $form->createView(),
            'ticket' => $ticket,
            'place' => $place,
            'reservations' => $reservations,
        ]);
    }
}
