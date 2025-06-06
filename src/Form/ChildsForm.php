<?php

namespace App\Form;

use App\Entity\Childs;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ChildsForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom')
            ->add('prenom')
            ->add('date_naissance')
            ->add('genre')
            ->add('allergies')
            ->add('remarques_medicales')
            ->add('revenus')
            ->add('date_entree')
            ->add('date_sortie')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Childs::class,
        ]);
    }
}
