<?php

namespace App\Controller\Admin;

use App\Entity\Advertising;
use App\Form\AdvertisingType;
use App\Manager\AdvertisingManager;
use Psr\Log\LoggerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdvertisingController extends AbstractController
{
    /**
     * @var AdvertisingManager
     */
    private $advertisingManager;
    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(AdvertisingManager $advertisingManager, LoggerInterface $logger)
    {
        $this->advertisingManager = $advertisingManager;
        $this->logger = $logger;
    }

    /**
     * @Route("/advertising", name="advertising")
     */
    public function index(Request $request)
    {
        $page = $request->query->get('page', 1);

        $pager = $this->advertisingManager->search([], null, $page);

        return $this->render('admin/advertising/index.html.twig', [
            'pager' => $pager,
        ]);
    }

    /**
     * @Route("/advertising/new", name="advertising_create")
     */
    public function create(Request $request)
    {
        $form = $this->createForm(AdvertisingType::class);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Advertising $advertising */
            $advertising = $form->getData();

            try {
                $this->advertisingManager->save($advertising);

                $this->addFlash('success', 'Advertising successfully saved');
            } catch (\Exception $exception) {
                $this->logger->error('Cannot save advertising', [
                    'exception_message' => $exception->getMessage(),
                    'exception_file' => $exception->getFile(),
                    'exception_line' => $exception->getLine(),
                ]);

                $this->addFlash('error', 'Cannot save advertising');
            }

            return $this->redirectToRoute('advertising');
        }

        return $this->render('admin/advertising/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/advertising/{id}/edit", name="advertising_edit")
     * @ParamConverter("advertising", class="App:Advertising")
     *
     * @param Request $request
     * @param Advertising $advertising
     *
     * @return RedirectResponse|Response
     */
    public function edit(Request $request, Advertising $advertising)
    {
        $form = $this->createForm(AdvertisingType::class, $advertising);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Advertising $advertising */
            $advertising = $form->getData();

            try {
                $this->advertisingManager->save($advertising);

                $this->addFlash('success', sprintf('Advertising #%d successfully updated', $advertising->getId()));
            } catch (\Exception $exception) {
                $this->logger->error(
                    'An error occurred during the attribute save. Check if advertising are unique',
                    [
                        'exception_type' => get_class($exception),
                        'exception_message' => $exception->getMessage(),
                        'exception_file' => $exception->getFile(),
                        'exception_line' => $exception->getLine(),
                    ]
                );

                $this->addFlash('error', 'Cannot save advertising');
            }

            return $this->redirectToRoute('advertising');
        }

        return $this->render('admin/advertising/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/advertising/{id}", name="advertising_delete", methods={"DELETE"}, condition="request.isXmlHttpRequest()")
     * @ParamConverter("advertising", class="App:Advertising")
     *
     * @param Request $request
     * @param Advertising $advertising
     *
     * @return JsonResponse
     */
    public function delete(Request $request, Advertising $advertising)
    {
        $advertisingId = $advertising->getId();
        try {
            $this->advertisingManager->delete($advertising);

            $this->addFlash('success', sprintf('Advertising #%d successfully deleted', $advertisingId));
        } catch (\Exception $exception) {
            $this->logger->error('Cannot delete attribute', [
                'exception_message' => $exception->getMessage(),
                'exception_file' => $exception->getFile(),
                'exception_line' => $exception->getLine(),
            ]);

            $this->addFlash('error', 'Cannot delete attribute');
        }

        return new JsonResponse(sprintf('Delete %d OK', $advertisingId));
    }
}
