<?php

namespace App\Form;

use App\Form\UserSelectorType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class InscriptionForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('child', ChildsForm::class, [
                'data' => $options['child']
            ])
            ->add('responsables', CollectionType::class, [
                'entry_type' => ResponsablesForm::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'prototype' => true,
                'label' => false,
                'required' => false,
                'row_attr' => ['class' => 'responsables-collection'],
                'attr' => ['class' => 'responsables-list'],
                'empty_data' => [],
                'prototype_name' => '__name__',
            ])
            // ->add('userSelector', UserSelectorType::class, [
            // 'mapped' => false
            // ])
            ->add('user', UsersForm::class, [
                'data' => $options['user']
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => null,
            'csrf_protection' => true,
            'csrf_field_name' => '_csrf_token',
            'csrf_token_id' => 'inscription_form',
            'child' => null,
            'user' => null,
            // 'userSelector' => null,
            'responsable' => null,
            'allow_extra_fields' => true,
            'validation_groups' => ['Default']
        ]);

        // $resolver->setRequired(['child', 'user', 'userSelector', 'responsable']);
        $resolver->setRequired(['child', 'user', 'responsable']);
    }
}