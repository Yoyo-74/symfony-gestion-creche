<?php

namespace App\Entity;

use App\Repository\CalendarChildsRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CalendarChildsRepository::class)]
class CalendarChilds
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTime $date = null;

    #[ORM\Column(type: Types::TIME_MUTABLE)]
    private ?\DateTime $heure_arrivee = null;

    #[ORM\Column(type: Types::TIME_MUTABLE)]
    private ?\DateTime $heure_depart = null;

    #[ORM\Column]
    private ?bool $ispresent = null;

    #[ORM\ManyToOne(inversedBy: 'calendarChilds')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Childs $child = null;

    #[ORM\ManyToOne(inversedBy: 'calendarChilds')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Calendar $idcalendar = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDate(): ?\DateTime
    {
        return $this->date;
    }

    public function setDate(\DateTime $date): static
    {
        $this->date = $date;

        return $this;
    }

    public function getHeureArrivee(): ?\DateTime
    {
        return $this->heure_arrivee;
    }

    public function setHeureArrivee(\DateTime $heure_arrivee): static
    {
        $this->heure_arrivee = $heure_arrivee;

        return $this;
    }

    public function getHeureDepart(): ?\DateTime
    {
        return $this->heure_depart;
    }

    public function setHeureDepart(\DateTime $heure_depart): static
    {
        $this->heure_depart = $heure_depart;

        return $this;
    }

    public function ispresent(): ?bool
    {
        return $this->ispresent;
    }

    public function setIspresent(bool $ispresent): static
    {
        $this->ispresent = $ispresent;

        return $this;
    }

    public function getChild(): ?Childs
    {
        return $this->child;
    }

    public function setChild(?Childs $child): static
    {
        $this->child = $child;

        return $this;
    }

    public function getIdcalendar(): ?Calendar
    {
        return $this->idcalendar;
    }

    public function setIdcalendar(?Calendar $idcalendar): static
    {
        $this->idcalendar = $idcalendar;

        return $this;
    }
}
