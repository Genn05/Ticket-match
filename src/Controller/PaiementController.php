<?php

// src/Controller/PaiementController.php

namespace App\Controller;

use App\Entity\Card;
use App\Entity\Paiement;
use App\Entity\Reservation;
use App\Form\PaiementType;
use App\Repository\PaiementRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PaiementController extends AbstractController
{
    #[Route('/paiement', name: 'app_paiement_index')]
    public function index(PaiementRepository $paiementRepository): Response
    {
        // Afficher uniquement les paiements de l'utilisateur connecté
        $user = $this->getUser();
        $paiements = $paiementRepository->findBy(['user' => $user]);

        // Render the view with the retrieved payments
        return $this->render('paiement/index.html.twig', [
            'paiements' => $paiements,
        ]);
    }

    #[Route('/paiement/{reservationId<\d+>}', name: 'app_paiement', methods: ['GET', 'POST'])]
    public function effectuerPaiement(
        Request $request,
        EntityManagerInterface $entityManager,
        PaiementRepository $paiementRepository,
        int $reservationId
    ): Response {
        // Retrieve the reservation
        $reservation = $entityManager->getRepository(Reservation::class)->find($reservationId);

        if (!$reservation) {
            throw $this->createNotFoundException('Réservation non trouvée');
        }

        // Check if payment has already been made
        $existingPaiements = $paiementRepository->findBy(['reservation' => $reservation]);
        if (count($existingPaiements) > 0) {
            $this->addFlash('danger', 'Le paiement a déjà été effectué pour cette réservation.');
            return $this->redirectToRoute('app_home');
        }

        // Create a new payment instance
        $paiement = new Paiement();
        $paiement->setMontant(0.00); // Set a default value for montant
        $paiement->setReservation($reservation);
        $paiement->setUser($this->getUser());
        $paiement->setDatePaiement(new \DateTime());
        $paiement->setStatus('En attente');
        $montantTotal = $reservation->getTicket()->getPrix() * $reservation->getQuantite();
        $paiement->setMontant($montantTotal);

        // Create the payment form
        $form = $this->createForm(PaiementType::class, $paiement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Retrieve the card based on the card number provided in the form
            $cardNumber = $paiement->getCardNumber();
            $card = $entityManager->getRepository(Card::class)->findOneBy(['cardNumber' => $cardNumber]);

            if (!$card) {
                $this->addFlash('error', 'Carte invalide. Veuillez sélectionner une carte valide.');
                return $this->redirectToRoute('app_paiement', ['reservationId' => $reservationId]);
            }

            $paiement->setCard($card);

            // Check if the card balance is sufficient
            if ($card->getBalance() < $paiement->getMontant()) {
                $this->addFlash('error', 'Solde insuffisant sur la carte sélectionnée.');
                return $this->redirectToRoute('app_paiement', ['reservationId' => $reservationId]);
            }

            // Mark the payment as completed
            $paiement->setStatus('Payé');

            // Deduct the amount from the card balance
            $card->setBalance($card->getBalance() - $paiement->getMontant());

            // Persist the changes
            $entityManager->persist($paiement);
            $entityManager->persist($card);
            $entityManager->flush();

            $this->addFlash('success', 'Paiement effectué avec succès.');
            // Redirect to 'Mes Tickets' page after successful payment
            return $this->redirectToRoute('app_mes_tickets');
        }

        return $this->render('paiement/form.html.twig', [
            'form' => $form->createView(),
            'reservation' => $reservation,
        ]);
    }

    #[Route('/paiement/edit/{id}', name: 'app_paiement_edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request,
        PaiementRepository $paiementRepository,
        EntityManagerInterface $entityManager,
        int $id
    ): Response {
        // Retrieve the payment by ID
        $paiement = $paiementRepository->find($id);

        if (!$paiement) {
            throw $this->createNotFoundException('Paiement non trouvé');
        }

        // Create the form to edit the payment
        $form = $this->createForm(PaiementType::class, $paiement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Save the changes
            $entityManager->flush();

            $this->addFlash('success', 'Paiement mis à jour avec succès.');
            return $this->redirectToRoute('app_paiement_index');
        }

        return $this->render('paiement/edit.html.twig', [
            'form' => $form->createView(),
            'paiement' => $paiement,
        ]);
    }

    #[Route('/paiement/new', name: 'app_paiement_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        // Create a new payment entity
        $paiement = new Paiement();
        $paiement->setMontant(0.00); // Set a default value for montant
        $paiement->setDatePaiement(new \DateTime());
        $paiement->setStatus('En attente');

        // Create the form for the payment
        $form = $this->createForm(PaiementType::class, $paiement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $card = $paiement->getCard();

            if (!$card) {
                $this->addFlash('error', 'Veuillez sélectionner une carte valide.');
                return $this->redirectToRoute('app_paiement_new');
            }

            $entityManager->persist($paiement);
            $entityManager->flush();

            $this->addFlash('success', 'Paiement créé avec succès.');
            return $this->redirectToRoute('app_paiement_index');
        }

        return $this->render('paiement/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/paiement/show/{id}', name: 'app_paiement_show')]
    public function show(Paiement $paiement): Response
    {
        return $this->render('paiement/show.html.twig', [
            'paiement' => $paiement,
        ]);
    }
}
