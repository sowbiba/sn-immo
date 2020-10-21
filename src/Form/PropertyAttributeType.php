<?php

namespace App\Form;

use App\Entity\Attribute;
use App\Entity\PropertyAttribute;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

class PropertyAttributeType extends AbstractType
{
    /**
     * @var TranslatorInterface
     */
    private $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'attribute',
                EntityType::class,
                [
                    'class' => Attribute::class,
                    'choice_label' => 'name',
                    'choice_attr' => function ($choice, $key, $value) {
                        return ['data-index' => $key, 'data-type' => $choice->getType()];
                    },
                    'attr' => [
                        'class' => 'property_attribute_selector',
                    ],
                ]
            )
        ;

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $propertyAttribute = $event->getData();
            $form = $event->getForm();
            $currency = $form->getConfig()->getOptions()['currency'];

            /**
             * @var PropertyAttribute $propertyAttribute
             */
            if ($propertyAttribute && $propertyAttribute->getAttribute()) {
                switch ($propertyAttribute->getAttribute()->getType()) {
                    case 'numeric':
                        $form->add('value', NumberType::class, ['html5' => true]);
                        break;
                    case 'amount':
                        $form->add('value', MoneyType::class, ['currency' => $currency]);
                        break;
                    case 'boolean':
                        $form->add('value', ChoiceType::class, [
                            'choices' => [
                                $this->translator->trans('No') => 0,
                                $this->translator->trans('Yes') => 1,
                            ],
                            'expanded' => false,
                            'multiple' => false,
                        ]);
                        break;
                    default:
                        $form->add('value', TextType::class);
                        break;
                }
            } else {
                $form->add('value', TextType::class);
            }
        });
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => PropertyAttribute::class,
            'currency' => 'EUR',
        ]);
    }
}
