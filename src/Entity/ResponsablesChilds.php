<?php

namespace App\Entity;

use App\Repository\ResponsablesChildsRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ResponsablesChildsRepository::class)]
class ResponsablesChilds
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $lien = null;

    #[ORM\ManyToOne(inversedBy: 'responsablesChilds')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Responsables $responsable = null;

    #[ORM\ManyToOne(inversedBy: 'responsablesChilds')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Childs $child = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLien(): ?string
    {
        return $this->lien;
    }

    public function setLien(string $lien): static
    {
        $this->lien = $lien;

        return $this;
    }

    public function getResponsable(): ?Responsables
    {
        return $this->responsable;
    }

    public function setResponsable(?Responsables $responsable): static
    {
        $this->responsable = $responsable;

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
}
