<?php

namespace App\Controller;

use App\Entity\Childs;
use App\Form\ChildsForm;
use App\Repository\ChildsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/childs/controller/c/r/u/d')]
final class ChildsControllerCRUDController extends AbstractController
{
    #[Route(name: 'app_childs_controller_c_r_u_d_index', methods: ['GET'])]
    public function index(ChildsRepository $childsRepository): Response
    {
        return $this->render('childs_controller_crud/index.html.twig', [
            'childs' => $childsRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_childs_controller_c_r_u_d_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $child = new Childs();
        $form = $this->createForm(ChildsForm::class, $child);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($child);
            $entityManager->flush();

            return $this->redirectToRoute('app_childs_controller_c_r_u_d_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('childs_controller_crud/new.html.twig', [
            'child' => $child,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_childs_controller_c_r_u_d_show', methods: ['GET'])]
    public function show(Childs $child): Response
    {
        return $this->render('childs_controller_crud/show.html.twig', [
            'child' => $child,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_childs_controller_c_r_u_d_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Childs $child, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ChildsForm::class, $child);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_childs_controller_c_r_u_d_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('childs_controller_crud/edit.html.twig', [
            'child' => $child,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_childs_controller_c_r_u_d_delete', methods: ['POST'])]
    public function delete(Request $request, Childs $child, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$child->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($child);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_childs_controller_c_r_u_d_index', [], Response::HTTP_SEE_OTHER);
    }
}
