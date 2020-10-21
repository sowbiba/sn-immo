<?php

namespace App\Controller\Admin;

use App\Entity\Attribute;
use App\Entity\Property;
use App\Entity\PropertyAttribute;
use App\Form\PropertiesFilterType;
use App\Form\PropertyType;
use App\Manager\AttributesManager;
use App\Manager\PropertiesManager;
use Psr\Log\LoggerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
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
     * @var LoggerInterface
     */
    private $logger;
    /**
     * @var AttributesManager
     */
    private $attributesManager;
    /**
     * @var TranslatorInterface
     */
    private $translator;

    public function __construct(
        PropertiesManager $propertiesManager,
        AttributesManager $attributesManager,
        TranslatorInterface $translator,
        LoggerInterface $logger
    ) {
        $this->propertiesManager = $propertiesManager;
        $this->attributesManager = $attributesManager;
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
        $criterias = $filterForm->isSubmitted() ? array_filter($filterForm->getData()) : [];

        $pager = $this->propertiesManager->search($criterias, [$sortField => $sortDirection], $page);

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

                $this->addFlash('success', 'Property successfully saved');
            } catch (\Exception $exception) {
                $this->logger->error('Cannot save property', [
                    'exception_message' => $exception->getMessage(),
                    'exception_file' => $exception->getFile(),
                    'exception_line' => $exception->getLine(),
                ]);

                $this->addFlash('error', 'Cannot save property');
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
                'currency' => $this->getParameter('currency'),
            ]
        );

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Property $property */
            $property = $form->getData();

            try {
                $this->propertiesManager->save($property);

                $this->addFlash('success', sprintf('Property #%d successfully updated', $property->getId()));
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
                'response' => $this->translator->trans('This attribute is already defined for ths property.'),
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
}
