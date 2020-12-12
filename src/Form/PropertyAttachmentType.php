<?php

namespace App\Form;

use App\Entity\PropertyAttachment;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

class PropertyAttachmentType extends AbstractType
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
                'slug',
                ChoiceType::class,
                [
                    'block_prefix' => 'hey',
                    'choices' => $this->getAttachmentSlugs($options),
                    'multiple' => false,
                ]
            )
            ->add('display_name')
            ->add('path', FileType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'attachment_type' => null,
            'additional_attachment_slugs' => null,
        ]);
    }

    /**
     * @param array $options
     * @return string[]
     */
    private function getAttachmentSlugs(array $options): array
    {
        $attachmentType = array_key_exists('attachment_type', $options) ? $options['attachment_type'] : null;
        $attachmentSlugs = PropertyAttachment::DEFAULT_ATTACHMENT_SLUGS;

        if (isset($options['additional_attachment_slugs']) && is_array($options['additional_attachment_slugs'])) {
            $extraSlugs = [];
            foreach($options['additional_attachment_slugs'] as $extraAttachmentSlug) {
                if (! array_key_exists($extraAttachmentSlug['type'], $extraSlugs)) {
                    $extraSlugs[$extraAttachmentSlug['type']] = [];
                }
                $extraSlugs[$extraAttachmentSlug['type']][] = $extraAttachmentSlug['slug'];
            }
            $attachmentSlugs = array_merge_recursive($attachmentSlugs, $extraSlugs);
        }

        if (array_key_exists($attachmentType, $attachmentSlugs) && !empty($attachmentSlugs[$attachmentType])) {
            $flippedSlugs = [];
            foreach ($attachmentSlugs[$attachmentType] as $key => $value) {
                $flippedSlugs[$this->translator->trans($value)] = $value;
            }
            return $flippedSlugs;
        }

        return [];
    }
}
