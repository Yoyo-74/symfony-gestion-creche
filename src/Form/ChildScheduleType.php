<?php

namespace App\Form;

use App\Entity\Childs;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;

class ChildScheduleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('date_debut', DateType::class, [
                'widget' => 'single_text'
            ])
            ->add('date_fin', DateType::class, [
                'widget' => 'single_text'
            ]);

        foreach (['lundi', 'mardi', 'mercredi', 'jeudi', 'vendredi'] as $jour) {
            $builder
                ->add($jour . '_a', TimeType::class, [
                    'widget' => 'choice',
                    'minutes' => [0, 30],
                    'hours' => range(7, 19),
                    'placeholder' => [
                    'hour' => 'HH',
                    'minute' => 'MM'
                    ],
                    'required' => false,
                    'attr' => [
                    'class' => 'time-input'
                    ]
                ])
                ->add($jour . '_d', TimeType::class, [
                    'widget' => 'choice',
                    'minutes' => [0, 30],
                    'hours' => range(7, 19),
                    'placeholder' => [
                    'hour' => 'HH',
                    'minute' => 'MM'
                    ],
                    'required' => false,
                    'attr' => [
                    'class' => 'time-input'
                    ]
                ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => null,
            'csrf_protection' => true,
            'allow_extra_fields' => true,
        ]);
    }
} 