<?php

namespace App\Entity;

use App\Repository\StadeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: StadeRepository::class)]
class Stade
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\Column(length: 255)]
    private ?string $ville = null;

    #[ORM\Column]
    private ?int $capacite = null;

    /**
     * @var Collection<int, Mattch>
     */
    #[ORM\OneToMany(targetEntity: Mattch::class, mappedBy: 'stade')]
    private Collection $mattches;

    public function __construct()
    {
        $this->mattches = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    public function getVille(): ?string
    {
        return $this->ville;
    }

    public function setVille(string $ville): static
    {
        $this->ville = $ville;

        return $this;
    }

    public function getCapacite(): ?int
    {
        return $this->capacite;
    }

    public function setCapacite(int $capacite): static
    {
        $this->capacite = $capacite;

        return $this;
    }

    /**
     * @return Collection<int, Mattch>
     */
    public function getMattches(): Collection
    {
        return $this->mattches;
    }

    public function addMattch(Mattch $mattch): static
    {
        if (!$this->mattches->contains($mattch)) {
            $this->mattches->add($mattch);
            $mattch->setStade($this);
        }

        return $this;
    }

    public function removeMattch(Mattch $mattch): static
    {
        if ($this->mattches->removeElement($mattch)) {
            // set the owning side to null (unless already changed)
            if ($mattch->getStade() === $this) {
                $mattch->setStade(null);
            }
        }

        return $this;
    }
}
