<?php

namespace App\Controller;

use App\Entity\CalendarChilds;
use App\Form\CalendarChildsForm;
use App\Repository\CalendarChildsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/calendar/childs')]
final class CalendarChildsController extends AbstractController
{
    #[Route(name: 'app_calendar_childs_index', methods: ['GET'])]
    public function index(CalendarChildsRepository $calendarChildsRepository): Response
    {
        return $this->render('calendar_childs/index.html.twig', [
            'calendar_childs' => $calendarChildsRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_calendar_childs_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {   
        $calendarChild = new CalendarChilds();
        $form = $this->createForm(CalendarChildsForm::class, $calendarChild);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($calendarChild);
            $entityManager->flush();

            return $this->redirectToRoute('app_calendar_childs_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('calendar_childs/new.html.twig', [
            'calendar_child' => $calendarChild,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_calendar_childs_show', methods: ['GET'])]
    public function show(CalendarChilds $calendarChild): Response
    {
        return $this->render('calendar_childs/show.html.twig', [
            'calendar_child' => $calendarChild,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_calendar_childs_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, CalendarChilds $calendarChild, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CalendarChildsForm::class, $calendarChild);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_calendar_childs_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('calendar_childs/edit.html.twig', [
            'calendar_child' => $calendarChild,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_calendar_childs_delete', methods: ['POST'])]
    public function delete(Request $request, CalendarChilds $calendarChild, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$calendarChild->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($calendarChild);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_calendar_childs_index', [], Response::HTTP_SEE_OTHER);
    }
}
