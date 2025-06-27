<?php

namespace App\Controller;

use DateTime;
use DatePeriod;
use DateInterval;
use App\Entity\Childs;
use App\Entity\Calendar;
use App\Entity\Responsables;
use App\Entity\CalendarChilds;
use App\Entity\ResponsablesChilds;
use App\Form\ChildsForm;
use App\Form\ResponsablesForm;
use App\Repository\ChildsRepository;
use App\Repository\ResponsablesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/childs')]
final class ChildsController extends AbstractController
{
    #[Route(name: 'app_childs_index', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('childs/index.html.twig');
    }

    #[Route('/new', name: 'app_childs_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $child = new Childs();
        $form = $this->createForm(ChildsForm::class, $child);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($child);
            $entityManager->flush();

            return $this->redirectToRoute('app_childs_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('childs/new.html.twig', [
            'child' => $child,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_childs_show', methods: ['GET'])]
    public function show(Childs $child): Response
    {
        $usersChilds = $child->getUsersChilds();
        $responsablesChilds = $child->getResponsablesChilds();

        return $this->render('childs/show.html.twig', [
            'child' => $child,
            'usersChilds' => $usersChilds,
            'responsablesChilds' => $responsablesChilds,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_childs_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Childs $child, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ChildsForm::class, $child);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                // Récupérer les dates et horaires
                $dateDebut = $form->get('date_debut')->getData();
                $dateFin = $form->get('date_fin')->getData();
                
                // Ne procéder à la mise à jour du calendrier que si les deux dates sont renseignées
                if ($dateDebut && $dateFin) {
                    $horaires = [
                        'lundi_a' => $form->get('lundi_a')->getData(),
                        'lundi_d' => $form->get('lundi_d')->getData(),
                        'mardi_a' => $form->get('mardi_a')->getData(),
                        'mardi_d' => $form->get('mardi_d')->getData(),
                        'mercredi_a' => $form->get('mercredi_a')->getData(),
                        'mercredi_d' => $form->get('mercredi_d')->getData(),
                        'jeudi_a' => $form->get('jeudi_a')->getData(),
                        'jeudi_d' => $form->get('jeudi_d')->getData(),
                        'vendredi_a' => $form->get('vendredi_a')->getData(),
                        'vendredi_d' => $form->get('vendredi_d')->getData(),
                    ];

                    // Vérifier que la date de fin est postérieure à la date de début
                    if ($dateFin < $dateDebut) {
                        throw new \Exception('La date de fin doit être postérieure à la date de début.');
                    }

                    // Supprimer les anciennes entrées du calendrier pour la période spécifiée
                    $calendarChildsRepo = $entityManager->getRepository(CalendarChilds::class);
                    $oldEntries = $calendarChildsRepo->createQueryBuilder('cc')
                        ->where('cc.child = :child')
                        ->andWhere('cc.date >= :dateDebut')
                        ->andWhere('cc.date <= :dateFin')
                        ->setParameter('child', $child)
                        ->setParameter('dateDebut', $dateDebut)
                        ->setParameter('dateFin', $dateFin)
                        ->getQuery()
                        ->getResult();

                    foreach ($oldEntries as $entry) {
                        $entityManager->remove($entry);
                    }
                    $entityManager->flush();

                    // Créer les nouvelles entrées du calendrier
                    $this->createCalendarEntries($child, $horaires, $dateDebut, $dateFin, $entityManager);
                }
                
                $entityManager->flush();

                $this->addFlash('success', 'Les modifications ont été enregistrées avec succès.');
                return $this->redirectToRoute('app_childs_index', [], Response::HTTP_SEE_OTHER);
            } catch (\Exception $e) {
                $this->addFlash('error', 'Une erreur est survenue : ' . $e->getMessage());
            }
        }

        return $this->render('childs/edit.html.twig', [
            'child' => $child,
            'form' => $form,
        ]);
    }

    private function createCalendarEntries(
        Childs $child,
        array $horaires,
        \DateTime $dateDebut,
        \DateTime $dateFin,
        EntityManagerInterface $entityManager
    ): void {
        $calendar = $entityManager->getRepository(Calendar::class);
        
        $interval = new \DateInterval('P1D');
        $period = new \DatePeriod($dateDebut, $interval, $dateFin);

        // Map des jours avec la première lettre en minuscule pour les clés des horaires
        $joursSemaine = [
            'Monday' => ['db' => 'Lundi', 'form' => 'lundi'],
            'Tuesday' => ['db' => 'Mardi', 'form' => 'mardi'],
            'Wednesday' => ['db' => 'Mercredi', 'form' => 'mercredi'],
            'Thursday' => ['db' => 'Jeudi', 'form' => 'jeudi'],
            'Friday' => ['db' => 'Vendredi', 'form' => 'vendredi']
        ];

        foreach ($period as $date) {
            $calendarDay = $calendar->findOneBy(['date' => $date]);
            
            if (!$calendarDay || !$calendarDay->isopen()) {
                continue;
            }

            $jour = $date->format('l'); // Récupère le jour en anglais
            if (isset($joursSemaine[$jour])) {
                $jourForm = $joursSemaine[$jour]['form'];
                $heureArriveeKey = $jourForm . '_a';
                $heureDepartKey = $jourForm . '_d';

                if (isset($horaires[$heureArriveeKey]) && isset($horaires[$heureDepartKey])) {
                    try {
                        $calendarChild = new CalendarChilds();
                        $calendarChild->setChild($child);
                        $calendarChild->setDate($date);
                        $calendarChild->setIdcalendar($calendarDay);
                        $calendarChild->setHeureArrivee($horaires[$heureArriveeKey]);
                        $calendarChild->setHeureDepart($horaires[$heureDepartKey]);
                        $calendarChild->setIspresent(false);
                        
                        $entityManager->persist($calendarChild);
                    } catch (\Exception $e) {
                        throw new \Exception('Erreur lors de la création du planning : ' . $e->getMessage());
                    }
                }
            }
        }
    }

    #[Route('/{id}', name: 'app_childs_delete', methods: ['POST'])]
    public function delete(Request $request, Childs $child, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$child->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($child);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_childs_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/remove-responsable/{responsableId}', name: 'app_childs_remove_responsable', methods: ['POST'])]
    public function removeResponsable(
        Childs $child,
        int $responsableId,
        EntityManagerInterface $entityManager,
        ResponsablesRepository $responsablesRepository
    ): JsonResponse {
        $responsable = $responsablesRepository->find($responsableId);
        
        if (!$responsable) {
            return new JsonResponse(['success' => false, 'message' => 'Responsable non trouvé'], 404);
        }

        // Chercher la relation ResponsablesChilds
        $responsablesChilds = $entityManager->getRepository(ResponsablesChilds::class)
            ->findOneBy(['child' => $child, 'responsable' => $responsable]);

        if (!$responsablesChilds) {
            return new JsonResponse(['success' => false, 'message' => 'Relation non trouvée'], 404);
        }

        $entityManager->remove($responsablesChilds);
        $entityManager->flush();

        return new JsonResponse(['success' => true]);
    }

    #[Route('/{id}/add-responsable-form', name: 'app_childs_add_responsable_form', methods: ['GET'])]
    public function addResponsableForm(
        Childs $child,
        ResponsablesRepository $responsablesRepository
    ): Response {
        // Récupérer tous les responsables qui ne sont pas déjà associés à cet enfant
        $existingResponsableIds = $child->getResponsablesChilds()
            ->map(fn($rc) => $rc->getResponsable()->getId())
            ->toArray();

        $availableResponsables = $responsablesRepository->createQueryBuilder('r')
            ->where('r.id NOT IN (:ids)')
            ->setParameter('ids', $existingResponsableIds ?: [0])
            ->getQuery()
            ->getResult();

        return $this->render('childs/_add_responsable_form.html.twig', [
            'child' => $child,
            'responsables' => $availableResponsables,
        ]);
    }

    #[Route('/{id}/add-responsable', name: 'app_childs_add_responsable', methods: ['POST'])]
    public function addResponsable(
        Request $request,
        Childs $child,
        EntityManagerInterface $entityManager,
        ResponsablesRepository $responsablesRepository
    ): JsonResponse {
        $responsableId = $request->request->get('responsable_id');
        $responsable = $responsablesRepository->find($responsableId);

        if (!$responsable) {
            return new JsonResponse(['success' => false, 'message' => 'Responsable non trouvé'], 404);
        }

        // Vérifier si la relation n'existe pas déjà
        $existingRelation = $entityManager->getRepository(ResponsablesChilds::class)
            ->findOneBy(['child' => $child, 'responsable' => $responsable]);

        if ($existingRelation) {
            return new JsonResponse(['success' => false, 'message' => 'Ce responsable est déjà associé à cet enfant'], 400);
        }

        // Créer la nouvelle relation
        $responsablesChilds = new ResponsablesChilds();
        $responsablesChilds->setChild($child);
        $responsablesChilds->setResponsable($responsable);

        $entityManager->persist($responsablesChilds);
        $entityManager->flush();

        return new JsonResponse([
            'success' => true,
            'responsable' => [
                'id' => $responsable->getId(),
                'nom' => $responsable->getNom(),
                'prenom' => $responsable->getPrenom(),
                'tel' => $responsable->getTel(),
            ],
        ]);
    }

    #[Route('/{id}/add-new-responsable', name: 'app_childs_add_new_responsable', methods: ['POST'])]
    public function addNewResponsable(
        Request $request,
        Childs $child,
        EntityManagerInterface $entityManager
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);
        
        if (!$data) {
            return new JsonResponse(['success' => false, 'message' => 'Données invalides'], 400);
        }

        try {
            // Créer le nouveau responsable
            $responsable = new Responsables();
            $responsable->setNom($data['nom']);
            $responsable->setPrenom($data['prenom']);
            $responsable->setEmail($data['email']);
            $responsable->setTel($data['tel']);
            
            $entityManager->persist($responsable);
            $entityManager->flush(); // Pour obtenir l'ID du responsable

            // Créer la relation ResponsablesChilds
            $responsablesChilds = new ResponsablesChilds();
            $responsablesChilds->setChild($child);
            $responsablesChilds->setResponsable($responsable);
            $responsablesChilds->setLien($data['lien']);
            
            $entityManager->persist($responsablesChilds);
            $entityManager->flush();

            return new JsonResponse([
                'success' => true,
                'responsable' => [
                    'id' => $responsable->getId(),
                    'nom' => $responsable->getNom(),
                    'prenom' => $responsable->getPrenom(),
                    'tel' => $responsable->getTel(),
                    'email' => $responsable->getEmail(),
                    'lien' => $data['lien']
                ]
            ]);

        } catch (\Exception $e) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Erreur lors de l\'ajout du responsable : ' . $e->getMessage()
            ], 500);
        }
    }
}
