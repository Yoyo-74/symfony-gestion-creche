<?php

namespace App\Entity;

use App\Repository\ChildsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ChildsRepository::class)]
class Childs
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\Column(length: 255)]
    private ?string $prenom = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTime $date_naissance = null;

    #[ORM\Column(length: 20)]
    private ?string $genre = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $allergies = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $remarques_medicales = null;

    #[ORM\Column(nullable: true)]
    private ?int $revenus = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTime $date_entree = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTime $date_sortie = null;

    /**
     * @var Collection<int, CalendarChilds>
     */
    #[ORM\OneToMany(targetEntity: CalendarChilds::class, mappedBy: 'child')]
    private Collection $calendarChilds;

    /**
     * @var Collection<int, ResponsablesChilds>
     */
    #[ORM\OneToMany(targetEntity: ResponsablesChilds::class, mappedBy: 'child')]
    private Collection $responsablesChilds;

    /**
     * @var Collection<int, UsersChilds>
     */
    #[ORM\OneToMany(targetEntity: UsersChilds::class, mappedBy: 'child')]
    private Collection $usersChilds;

    public function __construct()
    {
        $this->calendarChilds = new ArrayCollection();
        $this->responsablesChilds = new ArrayCollection();
        $this->usersChilds = new ArrayCollection();
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

    public function getDateNaissance(): ?\DateTime
    {
        return $this->date_naissance;
    }

    public function setDateNaissance(\DateTime $date_naissance): static
    {
        $this->date_naissance = $date_naissance;

        return $this;
    }

    public function getGenre(): ?string
    {
        return $this->genre;
    }

    public function setGenre(string $genre): static
    {
        $this->genre = $genre;

        return $this;
    }

    public function getAllergies(): ?string
    {
        return $this->allergies;
    }

    public function setAllergies(?string $allergies): static
    {
        $this->allergies = $allergies;

        return $this;
    }

    public function getRemarquesMedicales(): ?string
    {
        return $this->remarques_medicales;
    }

    public function setRemarquesMedicales(?string $remarques_medicales): static
    {
        $this->remarques_medicales = $remarques_medicales;

        return $this;
    }

    public function getRevenus(): ?int
    {
        return $this->revenus;
    }

    public function setRevenus(?int $revenus): static
    {
        $this->revenus = $revenus;

        return $this;
    }

    public function getDateEntree(): ?\DateTime
    {
        return $this->date_entree;
    }

    public function setDateEntree(?\DateTime $date_entree): static
    {
        $this->date_entree = $date_entree;

        return $this;
    }

    public function getDateSortie(): ?\DateTime
    {
        return $this->date_sortie;
    }

    public function setDateSortie(?\DateTime $date_sortie): static
    {
        $this->date_sortie = $date_sortie;

        return $this;
    }

    /**
     * @return Collection<int, CalendarChilds>
     */
    public function getCalendarChilds(): Collection
    {
        return $this->calendarChilds;
    }

    public function addCalendarChild(CalendarChilds $calendarChild): static
    {
        if (!$this->calendarChilds->contains($calendarChild)) {
            $this->calendarChilds->add($calendarChild);
            $calendarChild->setChild($this);
        }

        return $this;
    }

    public function removeCalendarChild(CalendarChilds $calendarChild): static
    {
        if ($this->calendarChilds->removeElement($calendarChild)) {
            // set the owning side to null (unless already changed)
            if ($calendarChild->getChild() === $this) {
                $calendarChild->setChild(null);
            }
        }

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
            $responsablesChild->setChild($this);
        }

        return $this;
    }

    public function removeResponsablesChild(ResponsablesChilds $responsablesChild): static
    {
        if ($this->responsablesChilds->removeElement($responsablesChild)) {
            // set the owning side to null (unless already changed)
            if ($responsablesChild->getChild() === $this) {
                $responsablesChild->setChild(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, UsersChilds>
     */
    public function getUsersChilds(): Collection
    {
        return $this->usersChilds;
    }

    public function addUsersChild(UsersChilds $usersChild): static
    {
        if (!$this->usersChilds->contains($usersChild)) {
            $this->usersChilds->add($usersChild);
            $usersChild->setChild($this);
        }

        return $this;
    }

    public function removeUsersChild(UsersChilds $usersChild): static
    {
        if ($this->usersChilds->removeElement($usersChild)) {
            // set the owning side to null (unless already changed)
            if ($usersChild->getChild() === $this) {
                $usersChild->setChild(null);
            }
        }

        return $this;
    }
}
