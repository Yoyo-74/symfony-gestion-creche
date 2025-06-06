<?php

namespace App\Entity;

use App\Repository\UsersChildsRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UsersChildsRepository::class)]
class UsersChilds
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'usersChilds')]
    private ?Childs $child = null;

    #[ORM\ManyToOne(inversedBy: 'usersChilds')]
    private ?Users $user = null;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getUser(): ?Users
    {
        return $this->user;
    }

    public function setUser(?Users $user): static
    {
        $this->user = $user;

        return $this;
    }
}
