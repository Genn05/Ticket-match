<?php

namespace App\Controller;

use App\Entity\User;

use App\Entity\Ticket;
use App\Form\TicketForm;
use App\Repository\ReservationRepository;
use App\Repository\TicketRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Finder\Exception\AccessDeniedException;

#[Route('/ticket')]
final class TicketController extends AbstractController
{
    // Afficher la liste de tous les tickets
    #[Route(name: 'app_ticket_index', methods: ['GET'])]
    public function index(TicketRepository $ticketRepository): Response
    {
        return $this->render('ticket/index.html.twig', [
            'tickets' => $ticketRepository->findAll(),
        ]);
    }

    #[Route('/user/{id}/reservations', name: 'app_user_reservations', methods: ['GET'])]
    public function userReservations(User $user): Response
    {
        $reservations = $user->getReservations();

        // Extract tickets from reservations
        $tickets = [];
        foreach ($reservations as $reservation) {
            $tickets[] = $reservation->getTicket();
        }

        return $this->render('ticket/reservations.html.twig', [
            'user' => $user,
            'tickets' => $tickets,
        ]);
    }

    #[Route('/reservation/{id}/delete', name: 'app_reservation_delete', methods: ['POST'])]
    public function deleteReservation(int $id, Request $request, EntityManagerInterface $entityManager): Response
    {
        $reservation = $entityManager->getRepository(\App\Entity\Reservation::class)->find($id);

        if (!$reservation) {
            throw $this->createNotFoundException('Réservation non trouvée.');
        }

        if ($this->isCsrfTokenValid('delete' . $reservation->getId(), $request->request->get('_token'))) {
            $quantityToDelete = (int) $request->request->get('quantity_to_delete', 1);
            $currentQuantity = $reservation->getQuantite();

            // Update the global ticket quantity by adding back the deleted quantity
            $ticket = $reservation->getTicket();
            $ticket->setQuantite($ticket->getQuantite() + $quantityToDelete);
            $entityManager->persist($ticket);

            if ($quantityToDelete >= $currentQuantity) {
                // Delete the entire reservation
                $entityManager->remove($reservation);
            } else {
                // Reduce the quantity of the reservation
                $reservation->setQuantite($currentQuantity - $quantityToDelete);
                $entityManager->persist($reservation);
            }
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_mes_tickets');
    }

    // Créer un nouveau ticket
    #[Route('/new', name: 'app_ticket_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
         if (!$this->isGranted('ROLE_ADMIN')) {
        throw new AccessDeniedException('Vous n\'êtes pas autorisé à modifier ce ticket.');
    }

        $ticket = new Ticket();
        $form = $this->createForm(TicketForm::class, $ticket);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($ticket);
            $entityManager->flush();

            return $this->redirectToRoute('app_ticket_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('ticket/new.html.twig', [
            'ticket' => $ticket,
            'form' => $form,
        ]);
    }

    // Afficher les tickets réservés par l'utilisateur connecté
 #[Route('/mes-tickets', name: 'app_mes_tickets')]
public function mesTickets(Security $security, \App\Repository\PaiementRepository $paiementRepository): Response
{
    $user = $security->getUser();

    if (!$user instanceof \App\Entity\User) {
        return $this->redirectToRoute('app_home');
    }

    $reservations = $user->getReservations();

    $tickets = [];
    $paiementsByReservation = [];

    foreach ($reservations as $reservation) {
        if ($reservation->getTicket()) {
            $tickets[] = $reservation->getTicket();
        }
        // Fetch payment for this reservation
        $paiement = $paiementRepository->findOneBy(['reservation' => $reservation]);
        $paiementsByReservation[$reservation->getId()] = $paiement;
    }

    return $this->render('ticket/mes_tickets.html.twig', [
        'tickets' => $tickets,
        'paiementsByReservation' => $paiementsByReservation,
        'reservations' => $reservations,
    ]);
}


    // Afficher un ticket spécifique
    #[Route('/{id}', name: 'app_ticket_show', methods: ['GET'])]
    public function show(Ticket $ticket): Response
    {
        return $this->render('ticket/show.html.twig', [
            'ticket' => $ticket,
        ]);
    }

    // Modifier un ticket
    #[Route('/{id}/edit', name: 'app_ticket_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Ticket $ticket, EntityManagerInterface $entityManager): Response
    {

         if (!$this->isGranted('ROLE_ADMIN')) {
        throw new AccessDeniedException('Vous n\'êtes pas autorisé à modifier ce ticket.');
    }

        $form = $this->createForm(TicketForm::class, $ticket);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_ticket_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('ticket/edit.html.twig', [
            'ticket' => $ticket,
            'form' => $form,
        ]);
    }

    // Supprimer un ticket
    #[Route('/{id}', name: 'app_ticket_delete', methods: ['POST'])]
    public function delete(Request $request, Ticket $ticket, EntityManagerInterface $entityManager): Response
    {
        // Vérification du token CSRF
        if ($this->isCsrfTokenValid('delete' . $ticket->getId(), $request->request->get('_token'))) {
            // Remove related reservations first to avoid foreign key constraint violation
            foreach ($ticket->getReservations() as $reservation) {
                $entityManager->remove($reservation);
            }
            $entityManager->remove($ticket);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_ticket_index', [], Response::HTTP_SEE_OTHER);
    }

    // Page de sélection de place pour un ticket
    #[Route('/{id}/select', name: 'app_ticket_select', methods: ['GET'])]
    public function select(Ticket $ticket, ReservationRepository $reservationRepository): Response
    {
        // Récupérer toutes les places déjà réservées pour ce ticket
        $reservedPlaces = $reservationRepository->createQueryBuilder('r')
            ->select('r.place')
            ->where('r.ticket = :ticket')
            ->andWhere('r.place IS NOT NULL')
            ->setParameter('ticket', $ticket)
            ->getQuery()
            ->getSingleColumnResult();

        // Calculer les coordonnées des sections (36 autour du stade)
        $sectionCoords = array();
        for ($n = 1; $n <= 36; $n++) {
            $angle = ($n-1)*10;
            $rad = $angle * pi() / 180;
            $x = 450 + 350 * cos($rad);
            $y = 350 + 260 * sin($rad);
            $sectionCoords[] = array(
                'label' => $n,
                'x' => round($x),
                'y' => round($y),
            );
        }

        // Calculer les coordonnées des places (60 autour du stade)
        $placeCoords = array();
        $totalPlaces = 60;
        for ($i = 0; $i < $totalPlaces; $i++) {
            $angle = $i * (360 / $totalPlaces);
            $rad = $angle * pi() / 180;
            $x = round(450 + 320 * cos($rad));
            $y = round(350 + 230 * sin($rad));
            $placeCoords[] = array(
                'name' => 'P-' . ($i+1),
                'x' => $x,
                'y' => $y,
            );
        }

        return $this->render('ticket/select.html.twig', [
            'ticket' => $ticket,
            'reserved_places' => $reservedPlaces,
            'section_coords' => $sectionCoords,
            'place_coords' => $placeCoords,
        ]);
    }

 
}
