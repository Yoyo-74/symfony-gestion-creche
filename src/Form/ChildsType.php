<?php

namespace App\Form;

use App\Entity\Users;
use App\Entity\Childs;
use App\Form\UserTypeForm;
use App\Entity\Responsables;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class ChildsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // Champs existants pour l'enfant
            ->add('nom')
            ->add('prenom')
            ->add('dateNaissance', DateType::class, [
                'widget' => 'single_text',
                'html5' => true,
                'attr' => ['class' => 'form-control'],
                'label' => 'Date de naissance'
            ])
            ->add('genre', ChoiceType::class, [
            'choices' => [
                'Fille' => 'F',
                'Garçon' => 'G'
            ],
            'expanded' => true,
            'multiple' => false,
            'required' => true,
            'label' => 'Genre'
            ])
            ->add('allergies', TextareaType::class, [
                'label' => 'Allergies',
                'required' => false
            ])
            ->add('remarquesMedicales', TextareaType::class, [
                'label' => 'Remarques médicales',
                'required' => false
            ])
            ->add('revenus', NumberType::class, [
                'label' => 'Revenus mensuels du foyer',
                'required' => false,
                'scale' => 2
            ])
            
            // Responsables existants
            // ->add('responsables', EntityType::class, [
            //     'class' => Responsables::class,
            //     'choice_label' => function(Responsables $responsable) {
            //         return $responsable->getNom() . ' ' . $responsable->getPrenom();
            //     },
            //     'multiple' => true,
            //     'expanded' => true,
            //     'required' => false,
            // ])
            
            // Nouveaux responsables
            // ->add('new_responsables', CollectionType::class, [
            //     'entry_type' => ResponsableType::class,
            //     'allow_add' => true,
            //     'allow_delete' => true,
            //     'by_reference' => false,
            //     'mapped' => false,
            // ])
            
            // Personnel existant
            // ->add('users', EntityType::class, [
            //     'class' => Users::class,
            //     'choice_label' => function(Users $user) {
            //         return $user->getNom() . ' ' . $user->getPrenom();
            //     },
            //     'multiple' => true,
            //     'expanded' => true,
            //     'required' => false,
            // ])
            
            // Nouveau personnel
            // ->add('new_users', CollectionType::class, [
            //     'entry_type' => UserTypeForm::class,
            //     'allow_add' => true,
            //     'allow_delete' => true,
            //     'by_reference' => false,
            //     'mapped' => false,
            // ])
            
            // Planning hebdomadaire
            ->add('planning', WeekPlanningType::class, [
                'mapped' => false,
            ])
            ->add('dateDebut', DateType::class, [
                'mapped' => false,
                'widget' => 'single_text',
                'html5' => true,
                'attr' => ['class' => 'form-control'],
                'label' => 'Date de début'
            ])
            ->add('dateFin', DateType::class, [
                'mapped' => false,
                'widget' => 'single_text',
                'html5' => true,
                'attr' => ['class' => 'form-control'],
                'label' => 'Date de fin'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Childs::class,
            // 'allow_new_responsable' => false,
            // 'allow_new_user' => false,
        ]);
    }
}