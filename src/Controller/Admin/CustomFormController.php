<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use App\Repository\CustomRepository;

class CustomFormController extends AbstractController
{
    #[Route('/admin/custom-form', name: 'custom_form')]
    // public function customForm(Request $request): Response
    // {
    //     $form = $this->createFormBuilder()
    //         ->add('nom', TextType::class, ['label' => 'Nom'])
    //         ->add('Envoyer', SubmitType::class)
    //         ->getForm();
        
    //     return $this->render('admin/custom_form.html.twig', [
    //         'form' => $form->createView(),
    //     ]);
    // }

    // public function customForm(CustomRepository $repository): Response
    // {
    //     $admins = $repository->findAdminUsers();

    //     return $this->render('admin/custom_form.html.twig', [
    //         'admins' => $admins,
    //     ]);
    // }
    // public function customForm(Request $request, CustomRepository $repository): Response
    // {
    //     // Récupérer les administrateurs
    //     $admins = $repository->findAdminUsers();

    //     // Créer le formulaire
    //     $form = $this->createFormBuilder()
    //         ->add('nom', TextType::class, ['label' => 'Nom'])
    //         ->add('Envoyer', SubmitType::class)
    //         ->getForm();

    //     return $this->render('admin/custom_form.html.twig', [
    //         'form' => $form->createView(),
    //         'admins' => $admins,
    //     ]);
    // }
    public function customForm(Request $request, CustomRepository $repository): Response
    {
        $form = $this->createFormBuilder()
            ->add('nom', TextType::class, ['label' => 'Nom'])
            ->add('Envoyer', SubmitType::class)
            ->getForm();

        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            // Traitement du formulaire
        }

        return $this->render('admin/custom_form.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
