<?php

namespace App\Form;

use App\Entity\Responsables;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class ResponsablesForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'required' => true,
                'row_attr' => ['class' => 'form-group responsable-field'],
                'attr' => ['class' => 'form-control']
            ])
            ->add('prenom', TextType::class, [
                'required' => true,
                'row_attr' => ['class' => 'form-group responsable-field'],
                'attr' => ['class' => 'form-control']
            ])
            ->add('email', EmailType::class, [
                'required' => false,
                'row_attr' => ['class' => 'form-group responsable-field'],
                'attr' => ['class' => 'form-control']
            ])
            ->add('tel', TextType::class, [
                'required' => false,
                'row_attr' => ['class' => 'form-group responsable-field'],
                'attr' => ['class' => 'form-control']
            ])
            // Le champ lien n'est plus dans l'entité Responsables
            ->add('lien', ChoiceType::class, [
                'mapped' => false, // Important : ce champ n'est pas mappé à l'entité
                'label' => 'Lien avec l\'enfant',
                'choices' => [
                    'Père' => 'père',
                    'Mère' => 'mère',
                    'Famille' => 'Famille',
                    'Tuteur légal' => 'Tuteur légal',
                    'Grand-parent' => 'Grand-parent',
                    'Autre' => 'autre'
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez sélectionner le lien avec l\'enfant'
                    ])
                ],
                'required' => true,
                'row_attr' => ['class' => 'form-group responsable-field'],
                'attr' => ['class' => 'form-control']
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Responsables::class,
            'csrf_protection' => false,
            'allow_extra_fields' => true,
        ]);
    }
}
