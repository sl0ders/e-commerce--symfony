<?php

namespace App\Controller\Admin;

use App\Datatables\OrderDatatable;
use App\Entity\Notification;
use App\Entity\Orders;
use App\Form\OrderChangeStateType;
use App\Repository\NotificationRepository;
use App\Repository\OrdersRepository;
use App\Services\EmailService;
use App\Services\NotificationServices;
use Exception;
use Knp\Bundle\SnappyBundle\Snappy\Response\PdfResponse;
use Knp\Snappy\Pdf;
use Sg\DatatablesBundle\Datatable\DatatableFactory;
use Sg\DatatablesBundle\Response\DatatableResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
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
        return $this->redirectToRoute('user_orders', ["id" => $this->getUser()->getId()]);
    }
}
