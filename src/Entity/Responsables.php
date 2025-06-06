<?php

namespace App\Entity;

use App\Repository\ResponsablesRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ResponsablesRepository::class)]
class Responsables
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\Column(length: 255)]
    private ?string $prenom = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $email = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $tel = null;

    /**
     * @var Collection<int, ResponsablesChilds>
     */
    #[ORM\OneToMany(targetEntity: ResponsablesChilds::class, mappedBy: 'responsable')]
    private Collection $responsablesChilds;

    public function __construct()
    {
        $this->responsablesChilds = new ArrayCollection();
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

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): static
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getTel(): ?string
    {
        return $this->tel;
    }

    public function setTel(?string $tel): static
    {
        $this->tel = $tel;

        return $this;
    }

    /**
     * @return Collection<int, ResponsablesChilds>
     */
    public function getResponsablesChilds(): Collection
    {
        return $this->responsablesChilds;
    }

    public function addResponsablesChild(ResponsablesChilds $responsablesChild): static
    {
        if (!$this->responsablesChilds->contains($responsablesChild)) {
            $this->responsablesChilds->add($responsablesChild);
            $responsablesChild->setResponsable($this);
        }

        return $this;
    }

    public function removeResponsablesChild(ResponsablesChilds $responsablesChild): static
    {
        if ($this->responsablesChilds->removeElement($responsablesChild)) {
            // set the owning side to null (unless already changed)
            if ($responsablesChild->getResponsable() === $this) {
                $responsablesChild->setResponsable(null);
            }
        }

        return $this;
    }
}
