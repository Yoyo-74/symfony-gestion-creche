<?php

namespace App\Controller;

use App\Entity\Childs;
use App\Entity\Calendar;
use App\Form\ChildsType;
// use App\Entity\UsersChilds;
// use App\Entity\ResponsablesChilds;
use App\Entity\CalendarChilds;
use App\Repository\ChildsRepository;
// use App\Repository\UsersRepository;
// use App\Repository\ResponsablesRepository;
use App\Repository\CalendarRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/childs')]
class ChildsController extends AbstractController
{
    #[Route('/new', name: 'app_childs_new', methods: ['GET', 'POST'])]
    public function new(
        Request $request, 
        EntityManagerInterface $em, 
        CalendarRepository $calendarRepository,
        // UsersRepository $usersRepository,
        // ResponsablesRepository $responsablesRepository
    ): Response {
        $child = new Childs();
        $form = $this->createForm(ChildsType::class, $child);
    //     $form = $this->createForm(ChildsType::class, $child, [
    //     'allow_new_responsable' => true,
    //     'allow_new_user' => true
    // ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Sauvegarde de l'enfant
            $em->persist($child);

            // Gestion des responsables
            // $responsables = $form->get('responsables')->getData();
            // $newResponsables = $form->get('new_responsables')->getData();
            
            // foreach ($newResponsables as $newResponsableData) {
            //     $responsable = new Responsables();
            //     $responsable->setNom($newResponsableData['nom']);
            //     $responsable->setPrenom($newResponsableData['prenom']);
            //     $responsable->setEmail($newResponsableData['email']);
            //     $responsable->setTelephone($newResponsableData['telephone']);
            //     $em->persist($responsable);
                
            //     $responsables[] = $responsable;
            // }

            // foreach ($responsables as $responsable) {
            //     $responsablesChilds = new ResponsablesChilds();
            //     $responsablesChilds->setChild($child);
            //     $responsablesChilds->setResponsable($responsable);
            //     $responsablesChilds->setLien($form->get('lien_' . $responsable->getId())->getData());
            //     $em->persist($responsablesChilds);
            // }

            // Gestion des users (personnel) existants et nouveaux
            // $users = $form->get('users')->getData();
            // $newUsers = $form->get('new_users')->getData();

            // foreach ($newUsers as $newUserData) {
            //     $user = new Users();
            //     $user->setEmail($newUserData['email']);
            //     $user->setRoles(['ROLE_STAFF']);
            //     $user->setPassword($this->passwordHasher->hashPassword($user, $newUserData['password']));
            //     $user->setNom($newUserData['nom']);
            //     $user->setPrenom($newUserData['prenom']);
            //     $em->persist($user);
                
            //     $users[] = $user;
            // }

            // foreach ($users as $user) {
            //     $usersChilds = new UsersChilds();
            //     $usersChilds->setChild($child);
            //     $usersChilds->setUser($user);
            //     $em->persist($usersChilds);
            // }

            // Récupération des données du planning
            $planning = $form->get('planning')->getData();
            $dateDebut = $form->get('dateDebut')->getData();
            $dateFin = $form->get('dateFin')->getData();

            // Création des entrées dans calendar_childs
            $this->createCalendarEntries(
                $child,
                $planning,
                $dateDebut,
                $dateFin,
                $em,
                $calendarRepository
            );

            $em->flush();

            return $this->redirectToRoute('app_childs_index');
        }

        return $this->render('childs/new.html.twig', [
            'child' => $child,
            'form' => $form,
        ]);
    }

    private function createCalendarEntries(
        Childs $child,
        array $planning,
        \DateTime $dateDebut,
        \DateTime $dateFin,
        EntityManagerInterface $em,
        CalendarRepository $calendarRepository
    ): void {
        $interval = new \DateInterval('P1D');
        $period = new \DatePeriod($dateDebut, $interval, $dateFin);
        
    // Tableau de correspondance anglais -> français
    $daysMap = [
        'Monday' => 'Lundi',
        'Tuesday' => 'Mardi',
        'Wednesday' => 'Mercredi',
        'Thursday' => 'Jeudi',
        'Friday' => 'Vendredi',
        'Saturday' => 'Samedi',
        'Sunday' => 'Dimanche'
    ];
    
        foreach ($period as $date) {
            $englishDay = $date->format('l');
            $frenchDay = $daysMap[$englishDay];
            
            if (isset($planning[$frenchDay.'_present']) && 
                $planning[$frenchDay.'_present'] && 
                $this->isCreheOpen($date, $calendarRepository)) {
                
                $calendar = $calendarRepository->findOneBy(['date' => $date]);
                if (!$calendar) {
                    continue;
                }

                $calendarChild = new CalendarChilds();
                $calendarChild->setChild($child);
                $calendarChild->setDate($date);
                $calendarChild->setIdcalendar($calendar);
                $calendarChild->setHeureArrivee($planning[$frenchDay.'_arrival']);
                $calendarChild->setHeureDepart($planning[$frenchDay.'_departure']);
                $calendarChild->setIspresent(false);
                
                $em->persist($calendarChild);
            }
        }
    }

    private function isCreheOpen(\DateTime $date, CalendarRepository $calendarRepository): bool
    {
        $calendar = $calendarRepository->findOneBy(['date' => $date]);
        return $calendar ? $calendar->isopen() : false;
    }

    #[Route('/{id}', name: 'app_childs_delete', methods: ['POST'])]
    public function delete(
        Request $request, 
        Childs $child, 
        EntityManagerInterface $em
    ): Response {
        if ($this->isCsrfTokenValid('delete'.$child->getId(), $request->request->get('_token'))) {
            // Suppression des relations
            // foreach ($child->getResponsablesChilds() as $responsablesChild) {
            //     $em->remove($responsablesChild);
            // }
            
            // foreach ($child->getUsersChilds() as $usersChild) {
            //     $em->remove($usersChild);
            // }
            
            // foreach ($child->getCalendarChilds() as $calendarChild) {
            //     $em->remove($calendarChild);
            // }

            // Suppression de l'enfant
            $em->remove($child);
            $em->flush();
            return $this->redirectToRoute('app_childs_index');
        }   
        return $this->render('childs/new.html.twig', [
        'child' => $child,
        'form' => $form,
    ]);
    }
    #[Route(name: 'app_childs_index', methods: ['GET'])]
    public function index(ChildsRepository $childsRepository): Response
    {
        return $this->render('childs_controller_crud/index.html.twig', [
            'childs' => $childsRepository->findAll(),
        ]);
    }

}