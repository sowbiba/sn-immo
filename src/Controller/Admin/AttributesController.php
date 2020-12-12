<?php

namespace App\Controller\Admin;

use App\Entity\Attribute;
use App\Form\AttributesFilterType;
use App\Form\AttributeType;
use App\Manager\AttributesManager;
use App\Manager\PropertyAttributesManager;
use Psr\Log\LoggerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AttributesController extends AbstractController
{
    /**
     * @var AttributesManager
     */
    private $attributesManager;
    /**
     * @var LoggerInterface
     */
    private $logger;
    /**
     * @var PropertyAttributesManager
     */
    private $propertyAttributesManager;

    public function __construct(AttributesManager $attributesManager, PropertyAttributesManager $propertyAttributesManager, LoggerInterface $logger)
    {
        $this->attributesManager = $attributesManager;
        $this->propertyAttributesManager = $propertyAttributesManager;
        $this->logger = $logger;
    }

    /**
     * @Route("/attributes", name="attributes")
     */
    public function index(Request $request)
    {
        $filterForm = $this->createForm(AttributesFilterType::class);
        $filterForm->handleRequest($request);

        $page = $request->query->get('page', 1);
        $sortField = $request->query->get('sort', 'id');
        $sortDirection = $request->query->get('direction', 'DESC');
        $criterias = $filterForm->isSubmitted() ? array_filter($filterForm->getData()) : [];

        $pager = $this->attributesManager->search($criterias, [$sortField => $sortDirection], $page);

        return $this->render('admin/attributes/index.html.twig', [
            'pager' => $pager,
            'sortField' => $sortField,
            'sortDirection' => $sortDirection,
            'filterForm' => $filterForm->createView(),
        ]);
    }

    /**
     * @Route("/attributes/new", name="attributes_create")
     */
    public function create(Request $request)
    {
        $form = $this->createForm(AttributeType::class);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Attribute $attribute */
            $attribute = $form->getData();

            try {
                $this->attributesManager->save($attribute);

                $this->addFlash('success', 'Attribute successfully saved');
            } catch (\Exception $exception) {
                $this->logger->error('Cannot save attribute', [
                    'exception_message' => $exception->getMessage(),
                    'exception_file' => $exception->getFile(),
                    'exception_line' => $exception->getLine(),
                ]);

                $this->addFlash('error', 'Cannot save attribute');
            }

            return $this->redirectToRoute('attributes');
        }

        return $this->render('admin/attributes/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/attributes/{id}/edit", name="attributes_edit")
     * @ParamConverter("attribute", class="App:Attribute")
     *
     * @param Request $request
     * @param Attribute $attribute
     *
     * @return RedirectResponse|Response
     */
    public function edit(Request $request, Attribute $attribute)
    {
        $form = $this->createForm(AttributeType::class, $attribute);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Attribute $attribute */
            $attribute = $form->getData();

            try {
                $this->attributesManager->save($attribute);

                $this->addFlash('success', sprintf('Attribute #%d successfully updated', $attribute->getId()));
            } catch (\Exception $exception) {
                $this->logger->error(
                    'An error occurred during the attribute save. Check if attributes are unique',
                    [
                        'exception_type' => get_class($exception),
                        'exception_message' => $exception->getMessage(),
                        'exception_file' => $exception->getFile(),
                        'exception_line' => $exception->getLine(),
                    ]
                );

                $this->addFlash('error', 'Cannot save attribute');
            }

            return $this->redirectToRoute('attributes');
        }

        return $this->render('admin/attributes/edit.html.twig', [
            'form' => $form->createView(),
            'isAttributeLinkedToAProperty' => $this->propertyAttributesManager->isAttributeLinkedToAProperty($attribute),
        ]);
    }

    /**
     * @Route("/attributes/{id}", name="attributes_delete", methods={"DELETE"}, condition="request.isXmlHttpRequest()")
     * @ParamConverter("attribute", class="App:Attribute")
     *
     * @param Request $request
     * @param Attribute $attribute
     *
     * @return JsonResponse
     */
    public function delete(Request $request, Attribute $attribute)
    {
        $attributeId = $attribute->getId();
        try {
            $this->attributesManager->delete($attribute);

            $this->addFlash('success', sprintf('Attribute #%d successfully deleted', $attributeId));
        } catch (\Exception $exception) {
            $this->logger->error('Cannot delete attribute', [
                'exception_message' => $exception->getMessage(),
                'exception_file' => $exception->getFile(),
                'exception_line' => $exception->getLine(),
            ]);

            $this->addFlash('error', 'Cannot delete attribute');
        }

        return new JsonResponse(sprintf('Delete %d OK', $attributeId));
    }
}
