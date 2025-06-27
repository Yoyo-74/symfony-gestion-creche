<?php

namespace App\Form;

use App\Entity\Users;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class UserSelectorType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('userChoice', ChoiceType::class, [
                'mapped' => false,  // Important : ne pas mapper ce champ
                'label' => 'Choisir un utilisateur',
                'choices' => [
                    'Créer un nouvel utilisateur' => 'new',
                    'Sélectionner un utilisateur existant' => 'existing'
                ],
                'expanded' => true,
                'multiple' => false,
                'data' => 'new'
            ])
            ->add('existingUser', EntityType::class, [
                'mapped' => false,  // Important : ne pas mapper ce champ
                'class' => Users::class,
                'choice_label' => function ($user) {
                    return $user->getNom() . ' ' . $user->getPrenom() . ' (' . $user->getEmail() . ')';
                },
                'required' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => null,
            'csrf_protection' => false
        ]);
    }
}