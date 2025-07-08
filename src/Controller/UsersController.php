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

    private function createCalendarEntries(Childs $child, array $scheduleData, \DateTime $dateDebut, \DateTime $dateFin, EntityManagerInterface $entityManager): void
    {
        $jours = ['lundi', 'mardi', 'mercredi', 'jeudi', 'vendredi'];
        $interval = new \DateInterval('P1D');
        $period = new \DatePeriod($dateDebut, $interval, (clone $dateFin)->modify('+1 day'));

        $calendarRepo = $entityManager->getRepository(Calendar::class);

        foreach ($period as $date) {
            $jourSemaine = strtolower($date->format('l'));
            $map = [
                'monday' => 'lundi',
                'tuesday' => 'mardi',
                'wednesday' => 'mercredi',
                'thursday' => 'jeudi',
                'friday' => 'vendredi',
            ];
            if (!isset($map[$jourSemaine])) continue;
            $jour = $map[$jourSemaine];
            $arriveeKey = $jour . '_a';
            $departKey = $jour . '_d';
            if (!empty($scheduleData[$arriveeKey]) && !empty($scheduleData[$departKey])) {
                $calendar = $calendarRepo->findOneBy([
                    'date' => $date,
                    'day' => ucfirst($jour),
                ]);

                if (!$calendar) {
                    throw new \LogicException("Aucune entrée Calendar pour la date " . $date->format('Y-m-d'));
                }

                $calendarChild = new CalendarChilds();
                $calendarChild->setIdcalendar($calendar);
                $calendarChild->setChild($child);
                $calendarChild->setDate(clone $date);
                $calendarChild->setHeureArrivee($scheduleData[$arriveeKey]);
                $calendarChild->setHeureDepart($scheduleData[$departKey]);
                $calendarChild->setIspresent(false);
                $entityManager->persist($calendarChild);
            }
        }
        $entityManager->flush();
    }

    #[Route('/{id}/add-child', name: 'app_users_add_child', methods: ['GET', 'POST'])]
    public function addChildForm(Request $request, Users $user, EntityManagerInterface $entityManager): Response
    {
        $child = new Childs();
        $form = $this->createForm(\App\Form\ChildsForm::class, $child);
        $form->handleRequest($request);

        // Gestion des responsables dynamiques (optionnel, à adapter si besoin)
        $responsablesData = $request->request->all('responsables');

        if ($form->isSubmitted() && $form->isValid()) {
            // Enfant
            $entityManager->persist($child);
            $entityManager->flush();

            // Lien UsersChilds
            $userChild = new UsersChilds();
            $userChild->setUser($user);
            $userChild->setChild($child);
            $entityManager->persist($userChild);
            $entityManager->flush();

            // Planning
            $dateDebut = $form->get('date_debut')->getData();
            $dateFin = $form->get('date_fin')->getData();
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
                $this->createCalendarEntries($child, $horaires, $dateDebut, $dateFin, $entityManager);
            }

            // Responsables (à adapter selon la structure du formulaire)
            if (!empty($responsablesData) && is_array($responsablesData)) {
                foreach ($responsablesData as $respData) {
                    if (empty($respData['nom']) || empty($respData['prenom']) || empty($respData['lien'])) {
                        continue;
                    }
                    $responsable = new Responsables();
                    $responsable->setNom($respData['nom']);
                    $responsable->setPrenom($respData['prenom']);
                    if (!empty($respData['email'])) $responsable->setEmail($respData['email']);
                    if (!empty($respData['tel'])) $responsable->setTel($respData['tel']);
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

            $this->addFlash('success', 'L\'enfant a bien été créé et rattaché à l\'utilisateur.');
            return $this->redirectToRoute('app_users_edit', ['id' => $user->getId()]);
        }

        return $this->render('childs/new_for_user.html.twig', [
            'user' => $user,
            'form' => $form,
            'responsablesData' => $responsablesData,
        ]);
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

