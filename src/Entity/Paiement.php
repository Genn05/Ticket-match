<?php

namespace App\Entity;

use App\Repository\PaiementRepository;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Card;
use App\Entity\User;
use App\Entity\Ticket;
use App\Entity\Reservation;

#[ORM\Entity(repositoryClass: PaiementRepository::class)]
class Paiement
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private ?int $id = null;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    private ?string $status = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: "paiements")]
    private ?User $user = null;

    #[ORM\ManyToOne(targetEntity: Ticket::class, inversedBy: "paiements")]
    private ?Ticket $ticket = null;

    #[ORM\ManyToOne(targetEntity: Reservation::class, inversedBy: "paiements")]
    private ?Reservation $reservation = null;

    #[ORM\ManyToOne(targetEntity: Card::class)]
    #[ORM\JoinColumn(name: "card_id", referencedColumnName: "id", nullable: false)]
    private ?Card $card = null;

    #[ORM\Column(type: "datetime")]
    private ?\DateTime $datePaiement = null;

    #[ORM\Column(type: "decimal", precision: 10, scale: 2)]
    private ?string $montant = null;

    // Getters and setters for each property

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;
        return $this;
    }

    public function getTicket(): ?Ticket
    {
        return $this->ticket;
    }

    public function setTicket(?Ticket $ticket): static
    {
        $this->ticket = $ticket;
        return $this;
    }

    public function getDatePaiement(): ?\DateTime
    {
        return $this->datePaiement;
    }

    public function setDatePaiement(\DateTime $datePaiement): static
    {
        $this->datePaiement = $datePaiement;
        return $this;
    }

    public function getMontant(): ?string
    {
        return $this->montant;
    }

    public function setMontant(string $montant): static
    {
        $this->montant = $montant;
        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(?string $status): static
    {
        $this->status = $status;
        return $this;
    }

    public function getReservation(): ?Reservation
    {
        return $this->reservation;
    }

    public function setReservation(?Reservation $reservation): static
    {
        $this->reservation = $reservation;
        return $this;
    }

    public function getCard(): ?Card
    {
        return $this->card;
    }

    public function setCard(?Card $card): static
    {
        $this->card = $card;
        return $this;
    }

    // Optionally: Add a convenience method to get the card number
    public function getCardNumber(): ?string
    {
        return $this->card ? $this->card->getCardNumber() : null;
    }
}
