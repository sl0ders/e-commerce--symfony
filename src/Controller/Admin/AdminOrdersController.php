<?php

namespace App\Controller\Admin;

use App\Datatables\OrderDatatable;
use App\Entity\Orders;
use App\Repository\OrdersRepository;
use Exception;
use Knp\Bundle\SnappyBundle\Snappy\Response\PdfResponse;
use Knp\Snappy\Pdf;
use Sg\DatatablesBundle\Datatable\DatatableFactory;
use Sg\DatatablesBundle\Response\DatatableResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @Route("/admin/orders")
 */
class AdminOrdersController extends AbstractController
{
    /**
     * @var DatatableFactory
     */
    private DatatableFactory $factory;

    /**
     * @var DatatableResponse
     */
    private DatatableResponse $response;

    public function __construct(DatatableFactory $factory, DatatableResponse $response)
    {
        $this->factory = $factory;
        $this->response = $response;
    }

    /**
     * @Route("/", name="admin_orders_index", methods={"GET"})
     * @param OrdersRepository $ordersRepository
     * @param Request $request
     * @return Response
     * @throws Exception
     */
    public function index(OrdersRepository $ordersRepository, Request $request): Response
    {
        $isAjax = $request->isXmlHttpRequest();
        $datatable = $this->factory->create(OrderDatatable::class);
        $datatable->buildDatatable();

        if ($isAjax) {
            $responseService = $this->response;
            $responseService->setDatatable($datatable);
            $responseService->getDatatableQueryBuilder();

            return $responseService->getResponse();
        }
        return $this->render('Admin/orders/index.html.twig', [
            'orders' => $ordersRepository->findAll(),
            'datatable' => $datatable
        ]);
    }

    /**
     * @Route("/{id}", name="admin_orders_show", methods={"GET"})
     * @param Orders $order
     * @param Pdf $knpSnappy_Pdf
     * @return PdfResponse
     */
    public function orderShow(Orders $order, Pdf $knpSnappy_Pdf): PdfResponse
    {
        $pdfName = $order->getNCmd();
        if (!"/path/to/the/$pdfName.pdf") {
            $knpSnappy_Pdf->generateFromHtml(
                $this->renderView(
                    'Admin/orders/show.html.twig',
                    array(
                        'order' => $order
                    )
                ),
                "/path/to/the/$pdfName.pdf"
            );
        }
        $html = $this->renderView('Admin/orders/show.html.twig', array(
            'order' => $order
        ));
        return new PdfResponse(
            $knpSnappy_Pdf->getOutputFromHtml($html),
            "$pdfName.pdf"
        );
    }


    /**
     * @Route("/{id}", name="admin_orders_delete", )
     * @param Request $request
     * @param Orders $order
     * @return Response
     */
    public function delete(Request $request, Orders $order): Response
    {
        if ($this->isCsrfTokenValid('delete' . $order->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($order);
            $entityManager->flush();
            $this->addFlash("success", "flash.order.deleteSuccessfully");
        }
        return $this->redirectToRoute('admin_orders_index');
    }
}
