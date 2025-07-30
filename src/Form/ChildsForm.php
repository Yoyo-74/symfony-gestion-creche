<?php

namespace App\Form;

use App\Entity\Childs;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\FormError;

class ChildsForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => 'Nom'
            ])
            ->add('prenom', TextType::class, [
                'label' => 'Prénom'
            ])
            ->add('date_naissance', DateType::class, [
                'label' => 'Date de naissance',
                'widget' => 'single_text'
            ])
            ->add('genre', ChoiceType::class, [
            'choices' => [
                'Fille' => 'F',
                'Garçon' => 'G'
            ],
            'expanded' => true,
            'multiple' => false
            ])
            ->add('allergies', TextareaType::class, [
                'label' => 'Allergies',
                'attr' => ['class' => 'flex-column'],
                'required' => false
            ])
            ->add('remarques_medicales', TextareaType::class, [
                'label' => 'Remarques médicales',
                'attr' => ['class' => 'flex-column'],
                'required' => false
            ])
            ->add('revenus', MoneyType::class, [
                'label' => 'Revenus',
                'required' => false,
                'currency' => 'EUR',
                'compound' => false
            ])
            ->add('date_entree', DateType::class, [
                'label' => 'Date d\'entrée',
                'widget' => 'single_text'
            ])
            ->add('date_sortie', DateType::class, [
                'label' => 'Date de sortie',
                'widget' => 'single_text',
                'required' => false
            ])
            ->add('date_debut', DateType::class, [
                'label' => 'Date de début',
                'widget' => 'single_text',
                'mapped' => false,
                'required' => false
            ])
            ->add('date_fin', DateType::class, [
                'label' => 'Date de fin',
                'widget' => 'single_text',
                'mapped' => false,
                'required' => false
            ])
            ->add('lundi_a', TimeType::class, [
            'widget' => 'choice',
            'minutes' => [0, 30],
            'hours' => range(7, 19),
            'label' => 'Lundi - Arrivée',
            'placeholder' => [
            'hour' => 'HH',
            'minute' => 'MM'
            ],
            'required' => false,
            'attr' => [
            'class' => 'time-input'
            ]
        ])
        ->add('lundi_d', TimeType::class, [
            'widget' => 'choice',
            'minutes' => [0, 30],
            'hours' => range(7, 19),
            'label' => 'Lundi - Départ',
            'placeholder' => [
            'hour' => 'HH',
            'minute' => 'MM'
            ],
            'required' => false,
            'attr' => [
            'class' => 'time-input'
            ]
        ])
            ->add('mardi_a', TimeType::class, [
            'widget' => 'choice',
            'minutes' => [0, 30],
            'hours' => range(7, 19),
            'label' => 'Mardi - Arrivée',
            'placeholder' => [
            'hour' => 'HH',
            'minute' => 'MM'
            ],
            'required' => false,
            'attr' => [
            'class' => 'time-input'
            ]
            ])
            ->add('mardi_d', TimeType::class, [
                'widget' => 'choice',
                'minutes' => [0, 30],
                'hours' => range(7, 19),
                'label' => 'Mardi - Départ',
                'placeholder' => [
                'hour' => 'HH',
                'minute' => 'MM'
                ],
                'required' => false,
                'attr' => [
                'class' => 'time-input'
                ]
            ])
            ->add('mercredi_a', TimeType::class, [
                'widget' => 'choice',
                'minutes' => [0, 30],
                'hours' => range(7, 19),
                'label' => 'Mercredi - Départ',
                'placeholder' => [
                'hour' => 'HH',
                'minute' => 'MM'
                ],
                'required' => false,
                'attr' => [
                'class' => 'time-input'
                ]
            ])
            ->add('mercredi_d', TimeType::class, [
                'widget' => 'choice',
                'minutes' => [0, 30],
                'hours' => range(7, 19),
                'label' => 'Mercredi - Arrivée',
                'placeholder' => [
                'hour' => 'HH',
                'minute' => 'MM'
                ],
                'required' => false,
                'attr' => [
                'class' => 'time-input'
                ]
            ])
            ->add('jeudi_a', TimeType::class, [
                'widget' => 'choice',
                'minutes' => [0, 30],
                'hours' => range(7, 19),
                'label' => 'Jeudi - Départ',
                'placeholder' => [
                'hour' => 'HH',
                'minute' => 'MM'
                ],
                'required' => false,
                'attr' => [
                'class' => 'time-input'
                ]
            ])
            ->add('jeudi_d', TimeType::class, [
                'widget' => 'choice',
                'minutes' => [0, 30],
                'hours' => range(7, 19),
                'label' => 'Jeudi - Arrivée',
                'placeholder' => [
                'hour' => 'HH',
                'minute' => 'MM'
                ],
                'required' => false,
                'attr' => [
                'class' => 'time-input'
                ]
            ])
            ->add('vendredi_a', TimeType::class, [
                'widget' => 'choice',
                'minutes' => [0, 30],
                'hours' => range(7, 19),
                'label' => 'Vendredi - Arrivée',
                'placeholder' => [
                'hour' => 'HH',
                'minute' => 'MM'
                ],
                'required' => false,
                'attr' => [
                'class' => 'time-input'
                ]
            ])
            ->add('vendredi_d', TimeType::class, [
                'widget' => 'choice',
                'minutes' => [0, 30],
                'hours' => range(7, 19),
                'label' => 'Vendredi - Départ',
                'placeholder' => [
                'hour' => 'HH',
                'minute' => 'MM'
                ],
                'required' => false,
                'attr' => [
                'class' => 'time-input'
                ]
            ])
        ;

        $builder->addEventListener(FormEvents::SUBMIT, function (FormEvent $event) {
            $data = $event->getData();
            $form = $event->getForm();
            
            $jours = ['lundi', 'mardi', 'mercredi', 'jeudi', 'vendredi'];
            
            foreach ($jours as $jour) {
                $arrivee = $data->{"get" . ucfirst($jour) . "A"}();
                $depart = $data->{"get" . ucfirst($jour) . "D"}();
                
                // Vérification si une seule heure est renseignée
                if (($arrivee && !$depart) || (!$arrivee && $depart)) {
                    $form[$jour . '_a']->addError(new FormError(
                        "Les heures d'arrivée et de départ doivent être toutes les deux renseignées pour le " . $jour
                    ));
                }
                
                // Vérification si l'heure de départ est après l'heure d'arrivée
                if ($arrivee && $depart && $depart <= $arrivee) {
                    $form[$jour . '_d']->addError(new FormError(
                        "L'heure de départ doit être après l'heure d'arrivée pour le " . $jour
                    ));
                }
            }
        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Childs::class,
            'csrf_protection' => false,
            'allow_extra_fields' => true,
        ]);
    }
}
