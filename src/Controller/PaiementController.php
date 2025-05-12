<?php

// src/Controller/PaiementController.php

namespace App\Controller;

use App\Entity\Paiement;
use App\Entity\Reservation;
use App\Form\PaiementForm;
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
        // Récupérer tous les paiements
        $paiements = $paiementRepository->findAll();

        // Renvoyer la vue avec les paiements récupérés
        return $this->render('paiement/index.html.twig', [
            'paiements' => $paiements,
        ]);
    }

    #[Route('/paiement/{reservationId<\d+>}', name: 'app_paiement', methods: ['GET', 'POST'])]
    public function effectuerPaiement(
        Request $request,
        EntityManagerInterface $entityManager,
        \App\Repository\PaiementRepository $paiementRepository,
        int $reservationId
    ): Response {
        // Récupérer la réservation
        $reservation = $entityManager->getRepository(Reservation::class)->find($reservationId);

        if (!$reservation) {
            throw $this->createNotFoundException('Réservation non trouvée');
        }

        // Vérifiez si le paiement a déjà été effectué
        $existingPaiements = $paiementRepository->findBy(['reservation' => $reservation]);
        if (count($existingPaiements) > 0) {
            $this->addFlash('error', 'Le paiement a déjà été effectué pour cette réservation.');
            return $this->redirectToRoute('app_home');
        }

        // Créer une nouvelle instance de Paiement
        $paiement = new Paiement();
        $paiement->setReservation($reservation);
        $paiement->setUser($this->getUser());
        $paiement->setDatePaiement(new \DateTime());
        $paiement->setStatus('En attente');
        $montantTotal = $reservation->getTicket()->getPrix() * $reservation->getQuantite();
        $paiement->setMontant($montantTotal);

        // Créer le formulaire de paiement
        $form = $this->createForm(PaiementForm::class, $paiement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Vérifiez le montant du paiement
            $montantPaye = $paiement->getMontant();
            if ($montantPaye != $reservation->getTicket()->getPrix() * $reservation->getQuantite()) {
                $this->addFlash('error', 'Le montant ne correspond pas au prix total du ticket.');
                return $this->redirectToRoute('app_paiement', ['reservationId' => $reservationId]);
            }

            // Marquer le paiement comme effectué
            $paiement->setStatus('Payé');

            // Mettre à jour l'état de la réservation et du ticket
            $ticket = $reservation->getTicket();
            if ($ticket->getQuantite() < $reservation->getQuantite()) {
                $this->addFlash('error', 'Il n\'y a pas assez de tickets disponibles.');
                return $this->redirectToRoute('app_paiement', ['reservationId' => $reservationId]);
            }

            $ticket->setQuantite($ticket->getQuantite() - $reservation->getQuantite()); // Mise à jour du stock de tickets

            // Persist et flush les entités
            $entityManager->persist($paiement);
            $entityManager->persist($ticket);
            $entityManager->flush();

            $this->addFlash('success', 'Paiement effectué avec succès.');
            return $this->redirectToRoute('app_mes_tickets');
        }

        $this->addFlash('info', 'Veuillez confirmer votre achat.');

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
        // Récupérer le paiement par ID
        $paiement = $paiementRepository->find($id);

        if (!$paiement) {
            throw $this->createNotFoundException('Paiement non trouvé');
        }

        // Créer le formulaire pour éditer le paiement
        $form = $this->createForm(PaiementForm::class, $paiement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Sauvegarder les modifications
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
        // Créer une nouvelle entité de Paiement
        $paiement = new Paiement();
        $paiement->setDatePaiement(new \DateTime()); // Définir la date du paiement par défaut
        $paiement->setStatus('En attente'); // Définir le statut initial

        // Créer le formulaire pour le paiement
        $form = $this->createForm(PaiementForm::class, $paiement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Persister la nouvelle entité de paiement
            $entityManager->persist($paiement);
            $entityManager->flush();

            $this->addFlash('success', 'Nouveau paiement créé avec succès.');
            return $this->redirectToRoute('app_paiement_index'); // Rediriger vers la liste des paiements
        }

        return $this->render('paiement/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }
   #[Route('/paiement/show/{id}', name: 'app_paiement_show')]
public function show(Paiement $paiement): Response
{
    if (!$paiement) {
        throw $this->createNotFoundException('Paiement non trouvé');
    }

    return $this->render('paiement/show.html.twig', [
        'paiement' => $paiement,
    ]);
}


}
