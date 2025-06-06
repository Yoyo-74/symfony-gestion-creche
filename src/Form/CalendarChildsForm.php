<?php

namespace App\Form;

use App\Entity\Calendar;
use App\Entity\CalendarChilds;
use App\Entity\Childs;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CalendarChildsForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('date')
            ->add('heure_arrivee')
            ->add('heure_depart')
            ->add('ispresent')
            ->add('child', EntityType::class, [
                'class' => Childs::class,
                'choice_label' => 'id',
            ])
            ->add('idcalendar', EntityType::class, [
                'class' => Calendar::class,
                'choice_label' => 'id',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => CalendarChilds::class,
        ]);
    }
}
