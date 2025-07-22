<?php

namespace App\Controller;

use App\Entity\Calendar;
use App\Form\CalendarForm;
use App\Repository\CalendarRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/calendar')]
final class CalendarController extends AbstractController
{
    #[Route(name: 'app_calendar_index', methods: ['GET'])]
    public function index(Request $request, CalendarRepository $calendarRepository): Response
    {
        $filterDate = $request->query->get('filter_date');
        if ($filterDate) {
            $calendars = $calendarRepository->createQueryBuilder('c')
                ->where('c.date >= :filterDate')
                ->setParameter('filterDate', $filterDate)
                ->orderBy('c.date', 'ASC')
                ->getQuery()
                ->getResult();
        } else {
            $calendars = $calendarRepository->findAll();
        }

        return $this->render('calendar/index.html.twig', [
            'calendars' => $calendars,
        ]);
    }

    #[Route('/new', name: 'app_calendar_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $calendar = new Calendar();
        $form = $this->createForm(CalendarForm::class, $calendar);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($calendar);
            $entityManager->flush();

            return $this->redirectToRoute('app_calendar_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('calendar/new.html.twig', [
            'calendar' => $calendar,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_calendar_show', methods: ['GET'])]
    public function show(Calendar $calendar): Response
    {
        return $this->render('calendar/show.html.twig', [
            'calendar' => $calendar,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_calendar_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Calendar $calendar, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CalendarForm::class, $calendar);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_calendar_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('calendar/edit.html.twig', [
            'calendar' => $calendar,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_calendar_delete', methods: ['POST'])]
    public function delete(Request $request, Calendar $calendar, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$calendar->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($calendar);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_calendar_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/toggle-isopen/{id}', name: 'calendar_toggle_isopen', methods: ['POST'])]
    public function toggleIsOpen(Request $request, Calendar $calendar, EntityManagerInterface $em): JsonResponse
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN'); // ou adapte selon tes droits

        $data = json_decode($request->getContent(), true);
        if (!isset($data['isopen'])) {
            return new JsonResponse(['success' => false], 400);
        }

        $calendar->setIsopen((bool)$data['isopen']);
        $em->flush();

        return new JsonResponse(['success' => true]);
    }
}
