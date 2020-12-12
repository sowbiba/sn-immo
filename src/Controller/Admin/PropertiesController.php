<?php

namespace App\Controller\Admin;

use App\Entity\Attribute;
use App\Entity\Property;
use App\Entity\PropertyAttachment;
use App\Entity\PropertyAttribute;
use App\Form\PropertiesFilterType;
use App\Form\PropertyAttachmentType;
use App\Form\PropertyType;
use App\Manager\AttributesManager;
use App\Manager\PropertiesManager;
use App\Uploader\PropertyAttachmentUploader;
use Psr\Log\LoggerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\File\Exception\UploadException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class PropertiesController extends AbstractController
{
    /**
     * @var PropertiesManager
     */
    private $propertiesManager;
    /**
     * @var AttributesManager
     */
    private $attributesManager;
    /**
     * @var PropertyAttachmentUploader
     */
    private $propertyAttachmentUploader;
    /**
     * @var TranslatorInterface
     */
    private $translator;
    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(
        PropertiesManager $propertiesManager,
        AttributesManager $attributesManager,
        PropertyAttachmentUploader $propertyAttachmentUploader,
        TranslatorInterface $translator,
        LoggerInterface $logger
    ) {
        $this->propertiesManager = $propertiesManager;
        $this->attributesManager = $attributesManager;
        $this->propertyAttachmentUploader = $propertyAttachmentUploader;
        $this->translator = $translator;
        $this->logger = $logger;
    }

    /**
     * @Route("/properties", name="properties")
     */
    public function index(Request $request)
    {
        $filterForm = $this->createForm(PropertiesFilterType::class);
        $filterForm->handleRequest($request);

        $page = $request->query->get('page', 1);
        $sortField = $request->query->get('sort', 'id');
        $sortDirection = $request->query->get('direction', 'DESC');
        $criteria = $filterForm->isSubmitted() ? array_filter($filterForm->getData()) : [];

        $pager = $this->propertiesManager->search($criteria, [$sortField => $sortDirection], $page);

        return $this->render('admin/properties/index.html.twig', [
            'pager' => $pager,
            'sortField' => $sortField,
            'sortDirection' => $sortDirection,
            'filterForm' => $filterForm->createView(),
        ]);
    }

    /**
     * @Route("/properties/new", name="properties_create")
     */
    public function create(Request $request)
    {
        $form = $this->createForm(PropertyType::class);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Property $property */
            $property = $form->getData();

            try {
                $this->propertiesManager->save($property);

                $this->addFlash('success', $this->translator->trans('Property successfully saved'));
            } catch (\Exception $exception) {
                $this->logger->error('Cannot save property', [
                    'exception_message' => $exception->getMessage(),
                    'exception_file' => $exception->getFile(),
                    'exception_line' => $exception->getLine(),
                ]);

                $this->addFlash('error', $this->translator->trans('An error occurred when saving'));
            }

            return $this->redirectToRoute('properties');
        }

        return $this->render('admin/properties/create.html.twig', [
            'form' => $form->createView(),
            'attributes' => $this->attributesManager->search(),
        ]);
    }

    /**
     * @Route("/properties/{id}/edit", name="properties_edit")
     * @ParamConverter("property", class="App:Property")
     */
    public function edit(Request $request, Property $property)
    {
        $form = $this->createForm(
            PropertyType::class,
            $property,
            [
                'currency' => $this->getParameter('app.currency'),
            ]
        );

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Property $property */
            $property = $form->getData();

            try {
                $this->propertiesManager->save($property);

                $this->addFlash('success', $this->translator->trans('Property #%id% successfully updated', ['%id%' => $property->getId()]));
            } catch (\Exception $exception) {
                $this->logger->error(
                    'An error occurred during the property save. Check if attributes are unique',
                    [
                        'exception_type' => get_class($exception),
                        'exception_message' => $exception->getMessage(),
                        'exception_file' => $exception->getFile(),
                        'exception_line' => $exception->getLine(),
                    ]
                );

                $this->addFlash('error', 'Cannot save property');
            }

            return $this->redirectToRoute('properties');
        }

        return $this->render('admin/properties/edit.html.twig', [
            'form' => $form->createView(),
            'attributes' => $this->attributesManager->search(),
            'attachmentTypes' => $this->getAttachmentTypes(),
            'propertyAttachments' => $property->getPropertyAttachments()->toArray(),
            'property' => $property,
        ]);
    }

    /**
     * @Route("/properties/{id}", name="properties_delete", methods={"DELETE"}, condition="request.isXmlHttpRequest()")
     * @ParamConverter("property", class="App:Property")
     */
    public function delete(Request $request, Property $property)
    {
        $propertyId = $property->getId();
        try {
            $this->propertiesManager->delete($property);

            $this->addFlash('success', sprintf('Property #%d successfully deleted', $propertyId));
        } catch (\Exception $exception) {
            $this->logger->error('Cannot delete property', [
                'exception_message' => $exception->getMessage(),
                'exception_file' => $exception->getFile(),
                'exception_line' => $exception->getLine(),
            ]);

            $this->addFlash('error', 'Cannot delete property');
        }

        return new JsonResponse(sprintf('Delete %d OK', $propertyId));
    }

    /**
     * @Route("/properties/{id}/attributes/{attributeId}/form",
     *     name="properties_attribute_form",
     *     methods={"GET"},
     *     condition="request.isXmlHttpRequest()",
     *     options = {"expose"=true},
     * )
     * @ParamConverter("property", class="App:Property")
     * @ParamConverter("attribute", class="App:Attribute", options={"id" = "attributeId"})
     */
    public function getPropertyAttributeForm(Request $request, Property $property, Attribute $attribute)
    {
        $attributesDefined = array_map(function (PropertyAttribute $propertyAttribute) {
            return $propertyAttribute->getAttribute()->getId();
        }, $property->getPropertyAttributes()->toArray());

        if (in_array($attribute->getId(), $attributesDefined, true)) {
            return new JsonResponse([
                'response' => $this->translator->trans('This attribute is already defined for this property.'),
            ], Response::HTTP_CONFLICT);
        }

        $formBuilder = $this->createFormBuilder()
            ->add('attribute', ChoiceType::class, [
                'choices' => [
                    $attribute->getName() => $attribute->getId(),
                ],
                'attr' => ['disabled' => true],
            ]);

        switch ($attribute->getType()) {
            case 'string':
                $formBuilder->add('value', TextType::class);
                break;
            case 'numeric':
                $formBuilder->add('value', NumberType::class);
                break;
            case 'amount':
                $formBuilder->add('value', MoneyType::class);
                break;
            case 'boolean':
                $formBuilder->add('value', ChoiceType::class, [
                    'choices' => [
                        $this->translator->trans('No') => 0,
                        $this->translator->trans('Yes') => 1,
                    ],
                    'expanded' => false,
                    'multiple' => false,
                ]);
                break;
        }

        return new JsonResponse([
            'response' => $this->renderView('admin/properties/propertyAttributeForm.html.twig', [
                'form' => $formBuilder->getForm()->createView(),
                'attributes' => $this->attributesManager->search(),
            ]),
        ], Response::HTTP_OK);
    }

    /**
     * @Route("/properties/{id}/attachments/{attachmentType}/form",
     *     name="properties_attachment_form",
     *     methods={"GET"},
     *     condition="request.isXmlHttpRequest()",
     *     options = {"expose"=true},
     * )
     * @ParamConverter("property", class="App:Property")
     */
    public function getPropertyAttachmentForm(Request $request, Property $property, string $attachmentType)
    {
        try {
            $form = $this->createForm(
                PropertyAttachmentType::class,
                null,
                [
                    'attachment_type' => $attachmentType,
                    'additional_attachment_slugs' => $this->getParameter('app.extra_attachment_slugs'),
                ]
            );
        } catch (\Exception $exception) {
            return new JsonResponse([
                'response' => $this->translator->trans($exception->getMessage()),
            ], Response::HTTP_BAD_REQUEST);
        }

        return new JsonResponse([
            'response' => $this->renderView('admin/properties/propertyAttachmentForm.html.twig', [
                'form' => $form->createView(),
                'formUniqueIdentifier' => uniqid(),
                'propertyId' => $property->getId(),
                'attachmentType' => $attachmentType,
            ]),
        ], Response::HTTP_OK);
    }

    /**
     * @Route("/properties/{id}/attachments/{attachmentType}/form",
     *     name="properties_attachment_save",
     *     methods={"POST"},
     *     condition="request.isXmlHttpRequest()",
     *     options = {"expose"=true},
     * )
     * @ParamConverter("property", class="App:Property")
     */
    public function savePropertyAttachmentForm(Request $request, Property $property, string $attachmentType)
    {
        try {
            $form = $this->createForm(
                PropertyAttachmentType::class,
                null,
                [
                    'attachment_type' => $attachmentType,
                    'additional_attachment_slugs' => $this->getParameter('app.extra_attachment_slugs'),
                ]
            );

            $form->handleRequest($request);

            if ($form->isValid()) {
                $fileName = $this->propertyAttachmentUploader->upload($attachmentType, $form->get('path')->getData());
            } else {
                throw new UploadException('Cannot upload property attachment');
            }

            $propertyAttachment = (new PropertyAttachment())
                ->setProperty($property)
                ->setType($attachmentType)
                ->setSlug($form->get('slug')->getData())
                ->setDisplayName($form->get('display_name')->getData())
                ->setPath($fileName)
            ;

            $this->propertiesManager->save($propertyAttachment);
        } catch (\Exception $exception) {
            return new JsonResponse([
                'response' => $this->translator->trans($exception->getMessage()),
            ], Response::HTTP_BAD_REQUEST);
        }

        return new JsonResponse([
            'response' => $this->translator->trans('Property attachment successfully saved.'),
            'replacement' =>$this->renderView('admin/properties/propertyAttachmentRow.html.twig', [
                'propertyAttachment' => $propertyAttachment,
            ])
        ], Response::HTTP_OK);
    }

    /**
     * @Route("/properties/attachments/{id}",
     *     name="properties_attachment_delete",
     *     methods={"DELETE"},
     *     condition="request.isXmlHttpRequest()",
     *     options = {"expose"=true},
     * )
     * @ParamConverter("propertyAttachment", class="App:PropertyAttachment")
     */
    public function deletePropertyAttachment(Request $request, PropertyAttachment $propertyAttachment)
    {
        $propertyAttachmentId = $propertyAttachment->getId();
        try {
            $this->propertiesManager->delete($propertyAttachment);

            return new JsonResponse([
                'response' => $this->translator->trans('Property attachment #%id% successfully deleted', ['%id%' => $propertyAttachmentId]),
            ], Response::HTTP_OK);
        } catch (\Exception $exception) {
            return new JsonResponse([
                'response' => $this->translator->trans('Cannot delete property attachment'),
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @return string[]
     */
    private function getAttachmentTypes(): array
    {
        $extraSlugs = [];
        foreach($this->getParameter('app.extra_attachment_slugs') as $extraAttachmentSlug) {
            if (! array_key_exists($extraAttachmentSlug['type'], $extraSlugs)) {
                $extraSlugs[$extraAttachmentSlug['type']] = [];
            }
            $extraSlugs[$extraAttachmentSlug['type']][] = $extraAttachmentSlug['slug'];
        }

        $definedSlugs = array_merge_recursive(
            PropertyAttachment::DEFAULT_ATTACHMENT_SLUGS,
            $extraSlugs
        );

        $attachmentTypes = [];
        foreach (array_keys($definedSlugs) as $type) {
            if (
                in_array($type, PropertyAttachment::ALLOWED_ATTACHMENT_TYPES, true)
                && !empty($definedSlugs[$type])
            ) {
                $attachmentTypes[] = $type;
            }
        }

        return $attachmentTypes;
    }
}
