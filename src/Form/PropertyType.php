<?php

namespace App\Form;

use App\Entity\Property;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PropertyType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title')
            ->add('address')
            ->add('zipcode')
            ->add('city')
            ->add('propertyAttributes', CollectionType::class, [
                'entry_type' => PropertyAttributeType::class,
                'entry_options' => ['label' => false, 'currency' => $options['currency']],
                'allow_add' => true,
                'allow_delete' => true,
                'prototype' => true,
                'by_reference' => false,
                'label' => false,
            ])
//            ->add('propertyAttachments', CollectionType::class, [
//                'entry_type' => PropertyAttachmentType::class,
//                'entry_options' => ['label' => false],
//                'allow_add' => true,
//                'allow_delete' => true,
//                'prototype' => true,
//                'by_reference' => false,
//                'label' => false,
//            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Property::class,
            'currency' => 'EUR',
        ]);
    }
}
