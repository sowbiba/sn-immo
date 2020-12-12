<?php

namespace App\Form;

use App\Entity\Attribute;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AttributeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('type', ChoiceType::class, [
                'choices' => array_combine(Attribute::ALLOWED_TYPES, Attribute::ALLOWED_TYPES),
            ])
        ;

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            /** @var Attribute $attribute */
            $attribute = $event->getData();
            $form = $event->getForm();

            // checks if the Attribute object is "new"
            // If no data is passed to the form, the data is "null".
            // This should be considered a new "Attribute"
            if (
                null !== $attribute
                && null !== $attribute->getId()
                && Attribute::TYPE_CHOICE === $attribute->getType()
            ) {
                $values = json_decode($attribute->getValues(), true);
                $form->add('values', ChoiceType::class, [
                    'choices' => array_combine($values, $values),
                ]);
            }
        });
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Attribute::class,
        ]);
    }
}
