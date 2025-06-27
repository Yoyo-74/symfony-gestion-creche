<?php

namespace App\Twig\Components;

use App\Repository\ChildsRepository;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent('child_search')]
class ChildSearchComponent
{
    use DefaultActionTrait;

    #[LiveProp(writable: true)]
    public string $search = '';

    #[LiveProp(writable: true)]
    public string $filter = 'all';

    public function __construct(
        private ChildsRepository $childsRepository
    ) {}

    public function getChilds(): array
    {
        $queryBuilder = $this->childsRepository->createQueryBuilder('c');

        if ($this->search) {
            $queryBuilder
                ->where('LOWER(c.nom) LIKE LOWER(:search)')
                ->orWhere('LOWER(c.prenom) LIKE LOWER(:search)')
                ->setParameter('search', '%' . strtolower($this->search) . '%');
        }

        if ($this->filter === 'active') {
            $queryBuilder->andWhere('c.date_sortie IS NULL');
        }

        return $queryBuilder
            ->orderBy('c.nom', 'ASC')
            ->getQuery()
            ->getResult();
    }
} 