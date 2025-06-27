<?php

namespace App\Twig\Components;

use App\Repository\UsersRepository;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent('user_search')]
class UserSearchComponent
{
    use DefaultActionTrait;

    #[LiveProp(writable: true)]
    public string $search = '';

    #[LiveProp(writable: true)]
    public string $roleFilter = 'all';

    private const AVAILABLE_ROLES = [
        'all' => 'Tous les rôles',
        'ROLE_ADMIN' => 'Administrateur',
        'ROLE_STAFF' => 'Personnel',
        'ROLE_PARENT' => 'Parent'
    ];

    public function __construct(
        private UsersRepository $usersRepository
    ) {}

    public function getUsers(): array
    {
        $queryBuilder = $this->usersRepository->createQueryBuilder('u');

        if ($this->search) {
            $queryBuilder
                ->where('LOWER(u.nom) LIKE LOWER(:search)')
                ->orWhere('LOWER(u.prenom) LIKE LOWER(:search)')
                ->setParameter('search', '%' . strtolower($this->search) . '%');
        }

        if ($this->roleFilter !== 'all') {
            $queryBuilder->andWhere('u.roles LIKE :role')
                        ->setParameter('role', '%' . $this->roleFilter . '%');
        }

        return $queryBuilder
            ->orderBy('u.nom', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function getAvailableRoles(): array
    {
        return self::AVAILABLE_ROLES;
    }
} 