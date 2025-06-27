<?php

namespace App\Controller;

use App\Entity\Responsables;
use App\Form\ResponsablesForm;
use App\Repository\ResponsablesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/responsables')]
final class ResponsablesController extends AbstractController
{
    #[Route(name: 'app_responsables_index', methods: ['GET'])]
    public function index(ResponsablesRepository $responsablesRepository): Response
    {
        return $this->render('responsables/index.html.twig', [
            'responsables' => $responsablesRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_responsables_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $responsable = new Responsables();
        $form = $this->createForm(ResponsablesForm::class, $responsable);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($responsable);
            $entityManager->flush();

            return $this->redirectToRoute('app_responsables_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('responsables/new.html.twig', [
            'responsable' => $responsable,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_responsables_show', methods: ['GET'])]
    public function show(Responsables $responsable): Response
    {
        return $this->render('responsables/show.html.twig', [
            'responsable' => $responsable,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_responsables_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Responsables $responsable, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ResponsablesForm::class, $responsable);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_responsables_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('responsables/edit.html.twig', [
            'responsable' => $responsable,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_responsables_delete', methods: ['POST'])]
    public function delete(Request $request, Responsables $responsable, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$responsable->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($responsable);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_responsables_index', [], Response::HTTP_SEE_OTHER);
    }
}
