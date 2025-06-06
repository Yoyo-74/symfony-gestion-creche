<?php

namespace App\Entity;

use App\Repository\CalendarRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CalendarRepository::class)]
class Calendar
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTime $date = null;

    #[ORM\Column(length: 20)]
    private ?string $day = null;

    #[ORM\Column]
    private ?int $week = null;

    #[ORM\Column]
    private ?bool $isopen = null;

    /**
     * @var Collection<int, CalendarChilds>
     */
    #[ORM\OneToMany(targetEntity: CalendarChilds::class, mappedBy: 'idcalendar')]
    private Collection $calendarChilds;

    public function __construct()
    {
        $this->calendarChilds = new ArrayCollection();
    }

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

    public function getDay(): ?string
    {
        return $this->day;
    }

    public function setDay(string $day): static
    {
        $this->day = $day;

        return $this;
    }

    public function getWeek(): ?int
    {
        return $this->week;
    }

    public function setWeek(int $week): static
    {
        $this->week = $week;

        return $this;
    }

    public function isopen(): ?bool
    {
        return $this->isopen;
    }

    public function setIsopen(bool $isopen): static
    {
        $this->isopen = $isopen;

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
            $calendarChild->setIdcalendar($this);
        }

        return $this;
    }

    public function removeCalendarChild(CalendarChilds $calendarChild): static
    {
        if ($this->calendarChilds->removeElement($calendarChild)) {
            // set the owning side to null (unless already changed)
            if ($calendarChild->getIdcalendar() === $this) {
                $calendarChild->setIdcalendar(null);
            }
        }

        return $this;
    }
}
