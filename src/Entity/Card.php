<?php

// src/Entity/Card.php

namespace App\Entity;

use App\Repository\CardRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection; // Import the Collection class

#[ORM\Entity(repositoryClass: CardRepository::class)]
class Card
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 19)]
    private ?string $cardNumber = null;

    #[ORM\Column(length: 50)]
    private ?string $cardType = null;

    // Relation inverse pour la relation OneToMany avec Paiement
    #[ORM\OneToMany(mappedBy: 'card', targetEntity: Paiement::class, orphanRemoval: true)]
    private Collection $paiements;  // Use Collection type for OneToMany relations

    public function __construct()
    {
        $this->paiements = new ArrayCollection(); // Initialize the collection
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCardNumber(): ?string
    {
        return $this->cardNumber;
    }

    public function setCardNumber(string $cardNumber): static
    {
        $this->cardNumber = $cardNumber;

        return $this;
    }

    public function getCardType(): ?string
    {
        return $this->cardType;
    }

    public function setCardType(string $cardType): static
    {
        $this->cardType = $cardType;

        return $this;
    }

    // Accessor for the paiements property
    public function getPaiements(): Collection
    {
        return $this->paiements;
    }

    public function setPaiements(Collection $paiements): static
    {
        $this->paiements = $paiements;

        return $this;
    }

    // Add or remove Paiement to the collection if needed
    public function addPaiement(Paiement $paiement): static
    {
        if (!$this->paiements->contains($paiement)) {
            $this->paiements[] = $paiement;
            $paiement->setCard($this);  // Ensure the reverse relation is set
        }

        return $this;
    }

    public function removePaiement(Paiement $paiement): static
    {
        if ($this->paiements->removeElement($paiement)) {
            // Set the owning side to null
            if ($paiement->getCard() === $this) {
                $paiement->setCard(null);
            }
        }

        return $this;
    }
}
