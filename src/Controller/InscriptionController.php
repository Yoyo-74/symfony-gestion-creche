<?php

namespace App\Controller;

use DateTime;
use DatePeriod;
use DateInterval;
use App\Entity\Users;
use App\Entity\Childs;
use App\Form\UsersForm;
use App\Entity\Calendar;
use App\Form\ChildsForm;
use App\Entity\UsersChilds;
use App\Entity\Responsables;
use App\Form\InscriptionForm;
use App\Entity\CalendarChilds;
use Psr\Log\LoggerInterface;
use App\Form\ResponsablesForm;
use App\Entity\ResponsablesChilds;
use Doctrine\ORM\EntityManagerInterface;
use App\Controller\InscriptionController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

final class InscriptionController extends AbstractController
{
    #[Route('/inscription', name: 'app_inscription', methods: ['GET', 'POST'])]
    public function new(
        Request $request, 
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $userPasswordHasher,
    ): Response {

        $child = new Childs();
        $user = new Users();

        $form = $this->createForm(InscriptionForm::class, null, [
            'child' => $child,
            'user' => $user,
            'responsable' => null,
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                // Validation des horaires
                $childData = $form->get('child')->getData();
                $validationErrors = $this->validateHoraires($childData);
                
                if (!empty($validationErrors)) {
                    foreach ($validationErrors as $error) {
                        $this->addFlash('error', $error);
                    }
                    return $this->render('inscription/index.html.twig', [
                        'form' => $form,
                        'debug' => false
                    ]);
                }

                // 1. Sauvegarde de l'enfant
                $child = $form->get('child')->getData();
                $entityManager->persist($child);
                $entityManager->flush(); // Flush pour avoir l'ID de l'enfant

                // 2. Sauvegarde des responsables
                foreach ($form->get('responsables') as $responsableForm) {
                    if ($responsableForm->getData()) {
                        $responsable = $responsableForm->getData();
                        $entityManager->persist($responsable);
                        $entityManager->flush(); // Flush pour avoir l'ID du responsable

                        // Création de la relation ResponsablesChilds
                        $responsablesChilds = new ResponsablesChilds();
                        $responsablesChilds->setChild($child);
                        $responsablesChilds->setResponsable($responsable);
                        $responsablesChilds->setLien($responsableForm->get('lien')->getData());
                        $entityManager->persist($responsablesChilds);
                    }
                }
                
                // 3. Sauvegarde de l'utilisateur
                $userData = $form->get('user')->getData();
                if ($userData) {
                    // Hash du mot de passe
                    $plainPassword = $form->get('user')->get('plainPassword')->getData();
                    if ($plainPassword) {
                        $userData->setPassword(
                            $userPasswordHasher->hashPassword($userData, $plainPassword)
                        );
                    }
                    
                    // Ajout du rôle parent par défaut
                    $userData->setRoles(['ROLE_USER', 'ROLE_PARENT']);
                    $entityManager->persist($userData);
                    $entityManager->flush();

                    // Création de la relation UsersChilds
                    $usersChilds = new UsersChilds();
                    $usersChilds->setChild($child);
                    $usersChilds->setUser($userData);
                    $entityManager->persist($usersChilds);
                    $entityManager->flush();
                }
                
                // Validation des horaires
                // $horaires = $form->get('horaires')->getData();
                // $this->validateHoraires($horaires);

                // 3. récupérer les date et horaires du formulaire Child
                $childForm = $form->get('child');

                // Récupérer les dates
                $dateDebut = $childForm->get('date_debut')->getData();
                $dateFin = $childForm->get('date_fin')->getData();
                
                // Récupérer les horaires
                $horaires = [
                    'lundi_a' => $childForm->get('lundi_a')->getData(),
                    'lundi_d' => $childForm->get('lundi_d')->getData(),
                    'mardi_a' => $childForm->get('mardi_a')->getData(),
                    'mardi_d' => $childForm->get('mardi_d')->getData(),
                    'mercredi_a' => $childForm->get('mercredi_a')->getData(),
                    'mercredi_d' => $childForm->get('mercredi_d')->getData(),
                    'jeudi_a' => $childForm->get('jeudi_a')->getData(),
                    'jeudi_d' => $childForm->get('jeudi_d')->getData(),
                    'vendredi_a' => $childForm->get('vendredi_a')->getData(),
                    'vendredi_d' => $childForm->get('vendredi_d')->getData(),
                ];


                // Création des entrées de calendrier
                $this->createCalendarEntries($child, $horaires, $dateDebut, $dateFin, $entityManager);
                $entityManager->flush();
                
                // Redirection vers la page d'index des enfants
                $this->addFlash('success', sprintf(
                    'L\'inscription de %s %s a été effectuée avec succès !',
                    $child->getPrenom(),
                    $child->getNom()
                ));
                return $this->redirectToRoute('app_childs_index');

            } catch (\Exception $e) {
                // Gestion des erreurs
                $this->addFlash('error', 'Une erreur est survenue : ' . $e->getMessage());
            }
        }
        
        return $this->render('inscription/index.html.twig', [
            'form' => $form,
            'debug' => false
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
                        $entityManager->flush();
                    } catch (\Exception $e) {
                        $this->addFlash('error', 'Erreur lors de la création du planning : ' . $e->getMessage());
                    }
                }
            }
        }
    }

    private function validateHoraires(Childs $child): array
    {
        $errors = [];
        $jours = [
            'lundi' => ['arrivee' => $child->getLundiA(), 'depart' => $child->getLundiD()],
            'mardi' => ['arrivee' => $child->getMardiA(), 'depart' => $child->getMardiD()],
            'mercredi' => ['arrivee' => $child->getMercrediA(), 'depart' => $child->getMercrediD()],
            'jeudi' => ['arrivee' => $child->getJeudiA(), 'depart' => $child->getJeudiD()],
            'vendredi' => ['arrivee' => $child->getVendrediA(), 'depart' => $child->getVendrediD()],
        ];

        foreach ($jours as $jour => $horaires) {
            $arrivee = $horaires['arrivee'];
            $depart = $horaires['depart'];

            if (($arrivee && !$depart) || (!$arrivee && $depart)) {
                $errors[] = sprintf(
                    "Pour le %s, vous devez renseigner à la fois l'heure d'arrivée et l'heure de départ",
                    ucfirst($jour)
                );
            }

            if ($arrivee && $depart && $depart <= $arrivee) {
                $errors[] = sprintf(
                    "Pour le %s, l'heure de départ doit être supérieure à l'heure d'arrivée",
                    ucfirst($jour)
                );
            }
        }

        return $errors;
    }

}
