<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;

class WeekPlanningType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $days = ['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi'];
        
        foreach ($days as $day) {
            $builder
                ->add($day.'_present', CheckboxType::class, [
                    'label' => $day,
                    'required' => false,
                ])
                ->add($day.'_arrival', TimeType::class, [
                    'label' => 'Arrivée',
                    'required' => false,
                    'widget' => 'single_text',
                ])
                ->add($day.'_departure', TimeType::class, [
                    'label' => 'Départ',
                    'required' => false,
                    'widget' => 'single_text',
                ]);
        }
    }
}