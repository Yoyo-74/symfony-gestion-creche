<?php

namespace App\Entity;

use App\Repository\ChildsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

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

    #[ORM\Column(type: Types::TIME_MUTABLE, nullable: true)]
    private ?\DateTime $lundi_a = null;

    #[ORM\Column(type: Types::TIME_MUTABLE, nullable: true)]
    private ?\DateTime $lundi_d = null;

    #[ORM\Column(type: Types::TIME_MUTABLE, nullable: true)]
    private ?\DateTime $mardi_a = null;

    #[ORM\Column(type: Types::TIME_MUTABLE, nullable: true)]
    private ?\DateTime $mardi_d = null;

    #[ORM\Column(type: Types::TIME_MUTABLE, nullable: true)]
    private ?\DateTime $mercredi_a = null;

    #[ORM\Column(type: Types::TIME_MUTABLE, nullable: true)]
    private ?\DateTime $mercredi_d = null;

    #[ORM\Column(type: Types::TIME_MUTABLE, nullable: true)]
    private ?\DateTime $jeudi_a = null;

    #[ORM\Column(type: Types::TIME_MUTABLE, nullable: true)]
    private ?\DateTime $jeudi_d = null;

    #[ORM\Column(type: Types::TIME_MUTABLE, nullable: true)]
    private ?\DateTime $vendredi_a = null;

    #[ORM\Column(type: Types::TIME_MUTABLE, nullable: true)]
    private ?\DateTime $vendredi_d = null;

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

    public function getLundiA(): ?\DateTime
    {
        return $this->lundi_a;
    }

    public function setLundiA(?\DateTime $lundi_a): static
    {
        $this->lundi_a = $lundi_a;

        return $this;
    }

    public function getLundiD(): ?\DateTime
    {
        return $this->lundi_d;
    }

    public function setLundiD(?\DateTime $lundi_d): static
    {
        $this->lundi_d = $lundi_d;

        return $this;
    }

    public function getMardiA(): ?\DateTime
    {
        return $this->mardi_a;
    }

    public function setMardiA(?\DateTime $mardi_a): static
    {
        $this->mardi_a = $mardi_a;

        return $this;
    }

    public function getMardiD(): ?\DateTime
    {
        return $this->mardi_d;
    }

    public function setMardiD(?\DateTime $mardi_d): static
    {
        $this->mardi_d = $mardi_d;

        return $this;
    }

    public function getMercrediA(): ?\DateTime
    {
        return $this->mercredi_a;
    }

    public function setMercrediA(?\DateTime $mercredi_a): static
    {
        $this->mercredi_a = $mercredi_a;

        return $this;
    }

    public function getMercrediD(): ?\DateTime
    {
        return $this->mercredi_d;
    }

    public function setMercrediD(?\DateTime $mercredi_d): static
    {
        $this->mercredi_d = $mercredi_d;

        return $this;
    }

    public function getJeudiA(): ?\DateTime
    {
        return $this->jeudi_a;
    }

    public function setJeudiA(?\DateTime $jeudi_a): static
    {
        $this->jeudi_a = $jeudi_a;

        return $this;
    }

    public function getJeudiD(): ?\DateTime
    {
        return $this->jeudi_d;
    }

    public function setJeudiD(?\DateTime $jeudi_d): static
    {
        $this->jeudi_d = $jeudi_d;

        return $this;
    }

    public function getVendrediA(): ?\DateTime
    {
        return $this->vendredi_a;
    }

    public function setVendrediA(?\DateTime $vendredi_a): static
    {
        $this->vendredi_a = $vendredi_a;

        return $this;
    }

    public function getVendrediD(): ?\DateTime
    {
        return $this->vendredi_d;
    }

    public function setVendrediD(?\DateTime $vendredi_d): static
    {
        $this->vendredi_d = $vendredi_d;

        return $this;
    }

    #[Assert\Callback]
    public function validateHoraires(ExecutionContextInterface $context): void
    {
        $jours = [
            'lundi' => ['arrivee' => $this->lundi_a, 'depart' => $this->lundi_d],
            'mardi' => ['arrivee' => $this->mardi_a, 'depart' => $this->mardi_d],
            'mercredi' => ['arrivee' => $this->mercredi_a, 'depart' => $this->mercredi_d],
            'jeudi' => ['arrivee' => $this->jeudi_a, 'depart' => $this->jeudi_d],
            'vendredi' => ['arrivee' => $this->vendredi_a, 'depart' => $this->vendredi_d],
        ];

        foreach ($jours as $jour => $horaires) {
            $arrivee = $horaires['arrivee'];
            $depart = $horaires['depart'];

            // Si l'une des heures est renseignée, l'autre doit l'être aussi
            if (($arrivee && !$depart) || (!$arrivee && $depart)) {
                $context->buildViolation(
                    "Pour le {$jour}, vous devez renseigner à la fois l'heure d'arrivée et l'heure de départ"
                )
                ->atPath($jour . '_a')
                ->addViolation();
            }

            // Si les deux heures sont renseignées, vérifier que départ > arrivée
            if ($arrivee && $depart) {
                if ($depart <= $arrivee) {
                    $context->buildViolation(
                        "Pour le {$jour}, l'heure de départ doit être supérieure à l'heure d'arrivée"
                    )
                    ->atPath($jour . '_d')
                    ->addViolation();
                }
            }
        }
    }
}
