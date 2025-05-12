<?php

namespace App\Entity;

use App\Repository\MattchRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MattchRepository::class)]
class Mattch
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $equipeA = null;

    #[ORM\Column(length: 255)]
    private ?string $equipeB = null;

    #[ORM\Column]
    private ?\DateTime $dateMatch = null;

    #[ORM\ManyToOne(inversedBy: 'mattches')]
    private ?Stade $stade = null;

    /**
     * @var Collection<int, Ticket>
     */
    #[ORM\OneToMany(targetEntity: Ticket::class, mappedBy: 'mattch')]
    private Collection $tickets;

#[ORM\Column(type: "string", nullable: true)]
    private ?string $imageEquipeExterieur = null;

#[ORM\Column(type: "string", nullable: true)]
    private ?string $imageEquipeDomicile = null;

    public function __construct()
    {
        $this->tickets = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEquipeA(): ?string
    {
        return $this->equipeA;
    }

    public function setEquipeA(string $equipeA): static
    {
        $this->equipeA = $equipeA;

        return $this;
    }

    public function getEquipeB(): ?string
    {
        return $this->equipeB;
    }

    public function setEquipeB(string $equipeB): static
    {
        $this->equipeB = $equipeB;

        return $this;
    }

    public function getDateMatch(): ?\DateTime
    {
        return $this->dateMatch;
    }

    public function setDateMatch(\DateTime $dateMatch): static
    {
        $this->dateMatch = $dateMatch;

        return $this;
    }

    public function getStade(): ?Stade
    {
        return $this->stade;
    }

    public function setStade(?Stade $stade): static
    {
        $this->stade = $stade;

        return $this;
    }

    /**
     * @return Collection<int, Ticket>
     */
    public function getTickets(): Collection
    {
        return $this->tickets;
    }

    public function addTicket(Ticket $ticket): static
    {
        if (!$this->tickets->contains($ticket)) {
            $this->tickets->add($ticket);
            $ticket->setMattch($this);
        }

        return $this;
    }

    public function removeTicket(Ticket $ticket): static
    {
        if ($this->tickets->removeElement($ticket)) {
            // set the owning side to null (unless already changed)
            if ($ticket->getMattch() === $this) {
                $ticket->setMattch(null);
            }
        }

        return $this;
    }

    public function getImageEquipeExterieur(): ?string
    {
        return $this->imageEquipeExterieur;
    }

    public function setImageEquipeExterieur(?string $imageEquipeExterieur): static
    {
        $this->imageEquipeExterieur = $imageEquipeExterieur;

        return $this;
    }

    public function getImageEquipeDomicile(): ?string
    {
        return $this->imageEquipeDomicile;
    }

    public function setImageEquipeDomicile(?string $imageEquipeDomicile): static
    {
        $this->imageEquipeDomicile = $imageEquipeDomicile;

        return $this;
    }
    public function __toString(): string
{
    return $this->equipeA . ' vs ' . $this->equipeB . ' - ' . $this->dateMatch->format('d/m/Y H:i');
}

}
