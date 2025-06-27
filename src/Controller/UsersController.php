<?php

namespace App\Controller;

use App\Entity\Users;
use App\Entity\Childs;
use App\Entity\Calendar;
use App\Entity\UsersChilds;
use App\Entity\Responsables;
use App\Entity\CalendarChilds;
use App\Entity\ResponsablesChilds;
use App\Form\UsersType;
use App\Form\UsersForm;
use App\Repository\UsersRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\VarDumper\VarDumper;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use App\Form\ChildScheduleType;

#[Route('/users')]
final class UsersController extends AbstractController
{
    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    #[Route(name: 'app_users_index', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('users/index.html.twig');
    }

    #[Route('/new', name: 'app_users_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager,
    UserPasswordHasherInterface $userPasswordHasher): Response
    {
        $user = new Users();
        $form = $this->createForm(UsersForm::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            /** @var string $plainPassword */
            $plainPassword = $form->get('plainPassword')->getData();

            // encode the plain password
            $user->setPassword($userPasswordHasher->hashPassword($user, $plainPassword));

            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('app_users_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('users/new.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_users_show', methods: ['GET'])]
    public function show(Users $user): Response
    {
        return $this->render('users/show.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_users_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Users $user, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(UsersForm::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_users_index', [], Response::HTTP_SEE_OTHER);
        }

        $scheduleForm = $this->createForm(ChildScheduleType::class, null, [
            'action' => '#',
            'method' => 'POST',
        ]);

        return $this->render('users/edit.html.twig', [
            'user' => $user,
            'form' => $form,
            'scheduleForm' => $scheduleForm,
        ]);
    }

    #[Route('/{id}', name: 'app_users_delete', methods: ['POST'])]
    public function delete(Request $request, Users $user, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_users_index', [], Response::HTTP_SEE_OTHER);
    }

    private function createCalendarEntries(
        Childs $child,
        array $horaires,
        \DateTime $dateDebut,
        \DateTime $dateFin,
        EntityManagerInterface $entityManager
    ): void {
        try {
            $calendar = $entityManager->getRepository(Calendar::class);
            
            $interval = new \DateInterval('P1D');
            $period = new \DatePeriod($dateDebut, $interval, $dateFin);

            // Map des jours avec correspondance entre anglais, français (BDD) et clés du formulaire
            $joursSemaine = [
                'Monday' => ['db' => 'Lundi', 'form' => 'lundi'],
                'Tuesday' => ['db' => 'Mardi', 'form' => 'mardi'],
                'Wednesday' => ['db' => 'Mercredi', 'form' => 'mercredi'],
                'Thursday' => ['db' => 'Jeudi', 'form' => 'jeudi'],
                'Friday' => ['db' => 'Vendredi', 'form' => 'vendredi']
            ];

            foreach ($period as $date) {
                $jour = $date->format('l'); // Récupère le jour en anglais
                if (!isset($joursSemaine[$jour])) {
                    continue; // Skip weekend
                }

                $jourFrancais = $joursSemaine[$jour]['db'];
                $jourForm = $joursSemaine[$jour]['form'];
                
                // Recherche dans Calendar avec le jour en français
                $calendarDay = $calendar->findOneBy([
                    'date' => $date,
                    'day' => $jourFrancais
                ]);
                
                if (!$calendarDay || !$calendarDay->isopen()) {
                    continue;
                }

                $heureArriveeKey = $jourForm . '_a';
                $heureDepartKey = $jourForm . '_d';

                if (isset($horaires[$heureArriveeKey]) && isset($horaires[$heureDepartKey])) {
                    try {
                        // Créer des objets DateTime pour les heures
                        $heureArrivee = new \DateTime();
                        $heureDepart = new \DateTime();
                        
                        // Extraire les heures et minutes
                        list($heureA, $minuteA) = explode(':', $horaires[$heureArriveeKey]);
                        list($heureD, $minuteD) = explode(':', $horaires[$heureDepartKey]);
                        
                        $heureArrivee->setTime((int)$heureA, (int)$minuteA);
                        $heureDepart->setTime((int)$heureD, (int)$minuteD);

                        $calendarChild = new CalendarChilds();
                        $calendarChild->setChild($child);
                        $calendarChild->setIdcalendar($calendarDay);
                        $calendarChild->setDate($date);
                        $calendarChild->setHeureArrivee($heureArrivee);
                        $calendarChild->setHeureDepart($heureDepart);
                        $calendarChild->setIspresent(false);
                        
                        $entityManager->persist($calendarChild);
                        $entityManager->flush();
                    } catch (\Exception $e) {
                        $this->logger->error("Erreur lors de la création de l'entrée du calendrier", [
                            'date' => $date->format('Y-m-d'),
                            'jour' => $jourFrancais,
                            'heureArrivee' => $horaires[$heureArriveeKey],
                            'heureDepart' => $horaires[$heureDepartKey],
                            'error' => $e->getMessage()
                        ]);
                    }
                }
            }
        } catch (\Exception $e) {
            $this->logger->error("Erreur globale dans createCalendarEntries", [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    #[Route('/{id}/add-child', name: 'app_users_add_child', methods: ['GET', 'POST'])]
    public function addChild(Request $request, Users $user, EntityManagerInterface $entityManager): Response
    {
        if (!$request->isXmlHttpRequest()) {
            return new Response('Cette route nécessite une requête AJAX', Response::HTTP_BAD_REQUEST);
        }

        try {
            // Vérification du token CSRF
            $submittedToken = $request->request->get('token');
            if (!$this->isCsrfTokenValid('add_child', $submittedToken)) {
                throw new \Exception('Token CSRF invalide');
            }

            $data = $request->request->all();
            
            // Log des données reçues
            $this->logger->debug('Données reçues dans addChild', [
                'data' => $data,
                'files' => $request->files->all()
            ]);

            // Validation des données requises
            $requiredFields = ['nom', 'prenom', 'date_naissance', 'genre', 'date_entree'];
            foreach ($requiredFields as $field) {
                if (empty($data[$field])) {
                    throw new \Exception("Le champ '$field' est requis");
                }
            }

            // Création de l'enfant
            $child = new Childs();
            $child->setNom($data['nom']);
            $child->setPrenom($data['prenom']);
            
            try {
                $dateNaissance = new \DateTime($data['date_naissance']);
                $child->setDateNaissance($dateNaissance);
            } catch (\Exception $e) {
                throw new \Exception("Format de date de naissance invalide: " . $e->getMessage());
            }
            
            $child->setGenre($data['genre']);
            
            try {
                $dateEntree = new \DateTime($data['date_entree']);
                $child->setDateEntree($dateEntree);
            } catch (\Exception $e) {
                throw new \Exception("Format de date d'entrée invalide: " . $e->getMessage());
            }

            // Champs optionnels
            if (!empty($data['allergies'])) {
                $child->setAllergies($data['allergies']);
            }
            if (!empty($data['remarques_medicales'])) {
                $child->setRemarquesMedicales($data['remarques_medicales']);
            }
            if (!empty($data['revenus'])) {
                $child->setRevenus((float)$data['revenus']);
            }

            // Persistance de l'enfant
            $entityManager->persist($child);
            $entityManager->flush();

            // Création de la relation UsersChilds
            $userChild = new UsersChilds();
            $userChild->setUser($user);
            $userChild->setChild($child);
            $entityManager->persist($userChild);
            $entityManager->flush();

            // Création des entrées CalendarChilds
            if (!empty($data['child_schedule'])) {
                $this->logger->debug('Données du planning reçues', [
                    'child_schedule' => $data['child_schedule']
                ]);

                if (empty($data['child_schedule']['date_debut']) || empty($data['child_schedule']['date_fin'])) {
                    throw new \Exception('Les dates de début et de fin sont requises pour le planning');
                }

                try {
                    $dateDebut = new \DateTime($data['child_schedule']['date_debut']);
                    $dateFin = new \DateTime($data['child_schedule']['date_fin']);
                    
                    // Récupérer les horaires directement du formulaire
                    $horaires = [];
                    foreach (['lundi', 'mardi', 'mercredi', 'jeudi', 'vendredi'] as $jour) {
                        $arriveeKey = $jour . '_a';
                        $departKey = $jour . '_d';
                        
                        if (!empty($data['child_schedule'][$arriveeKey]) && !empty($data['child_schedule'][$departKey])) {
                            $horaires[$arriveeKey] = $data['child_schedule'][$arriveeKey];
                            $horaires[$departKey] = $data['child_schedule'][$departKey];
                        }
                    }

                    $this->createCalendarEntries($child, $horaires, $dateDebut, $dateFin, $entityManager);
                } catch (\Exception $e) {
                    $this->logger->error("Erreur lors de la création des entrées du calendrier", [
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ]);
                    throw new \Exception("Erreur lors de la création du planning : " . $e->getMessage());
                }
            }

            // Traitement des responsables
            if (isset($data['responsables']) && is_array($data['responsables'])) {
                foreach ($data['responsables'] as $respData) {
                    if (empty($respData['nom']) || empty($respData['prenom']) || empty($respData['lien'])) {
                        continue;
                    }

                    $responsable = new Responsables();
                    $responsable->setNom($respData['nom']);
                    $responsable->setPrenom($respData['prenom']);
                    
                    if (!empty($respData['email'])) {
                        $responsable->setEmail($respData['email']);
                    }
                    if (!empty($respData['tel'])) {
                        $responsable->setTel($respData['tel']);
                    }

                    $entityManager->persist($responsable);
                    $entityManager->flush();

                    $responsableChild = new ResponsablesChilds();
                    $responsableChild->setResponsable($responsable);
                    $responsableChild->setChild($child);
                    $responsableChild->setLien($respData['lien']);
                    
                    $entityManager->persist($responsableChild);
                    $entityManager->flush();
                }
            }

            return $this->json([
                'success' => true,
                'message' => 'Enfant ajouté avec succès',
                'childId' => $child->getId()
            ]);

        } catch (\Exception $e) {
            $this->logger->error('Erreur lors de l\'ajout de l\'enfant', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return $this->json([
                'success' => false,
                'message' => $e->getMessage()
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/{id}/remove-child', name: 'app_users_remove_child', methods: ['POST'])]
    public function removeChild(Request $request, Users $user, EntityManagerInterface $entityManager): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $childId = $data['childId'] ?? null;

        if (!$childId) {
            return $this->json([
                'success' => false,
                'message' => 'ID de l\'enfant manquant'
            ]);
        }

        $userChild = $entityManager->getRepository(UsersChilds::class)->findOneBy([
            'user' => $user,
            'child' => $childId
        ]);

        if (!$userChild) {
            return $this->json([
                'success' => false,
                'message' => 'Association utilisateur-enfant non trouvée'
            ]);
        }

        try {
            $entityManager->remove($userChild);
            $entityManager->flush();
            return $this->json(['success' => true]);
        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'message' => 'Une erreur est survenue lors de la suppression'
            ]);
        }
    }
}

