<?php

namespace App\Controller\Admin;

use App\Datatables\OrderDatatable;
use App\Entity\Notification;
use App\Entity\Orders;
use App\Form\OrderChangeStateType;
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
     * @var DatatableFactory
     */
    private DatatableFactory $factory;

    /**
     * @var DatatableResponse
     */
    private DatatableResponse $response;
    private TranslatorInterface $translator;

    public function __construct(DatatableFactory $factory, DatatableResponse $response, TranslatorInterface $translator)
    {
        $this->factory = $factory;
        $this->response = $response;
        $this->translator = $translator;
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
     * @Route("/{id}", name="admin_orders_show_pdf", methods={"GET"})
     * @param Orders $order
     * @param Pdf $knpSnappy_Pdf
     * @return PdfResponse
     */
    public function pdfShow(Orders $order, Pdf $knpSnappy_Pdf): PdfResponse
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
        $html = $this->renderView('Admin/orders/pdf-show.html.twig', array(
            'order' => $order
        ));
        return new PdfResponse(
            $knpSnappy_Pdf->getOutputFromHtml($html),
            "$pdfName.pdf"
        );
    }

    #[Route("/show/{id}/{id-notification}", name: "admin_orders_show")]
    public function show(Orders $order): Response
    {
        return $this->render("Admin/orders/show.html.twig", [
            "order" => $order
        ]);
    }

    /**
     * @throws Exception
     */
    #[Route("/change-state/{id}", name: "admin_order_changeState")]
    public function changeStates(Orders $order, Request $request, NotificationServices $notificationServices, EmailService $emailService): RedirectResponse|Response
    {
        $formStatus = $this->createForm(OrderChangeStateType::class, $order);
        $formStatus->handleRequest($request);
        if ($formStatus->isSubmitted() && $formStatus->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $notificationServices->newNotification($this->translator->trans("notification.orders.changeState", ["%orderNumber%" => $order->getNCmd()], "NegasProjectTrans"), [$order->getUser()], ['orders_show', $order->getId()]);
            $emailSubject = $this->translator->trans("email.order.title", ["%orderNumber%" => $order->getNCmd()], "NegasProjectTrans");
            if ($formStatus->getData()->getValidation() != $order->getValidation()) {
                switch ($formStatus->getData()->getValidation()) {
                    case Orders::STATE_VALIDATE:
                        $order->setValidation($this->translator->trans(Orders::STATE_VALIDATE, [], "NegasProjectTrans"));
                        $emailService->sendMail($emailSubject, [$order->getUser()->getEmail()], ["orderValidate" => true, "order" => $order->getNCmd()]);
                        break;
                    case Orders::STATE_COMPLETED:
                        $order->setValidation($this->translator->trans(Orders::STATE_COMPLETED, [], "NegasProjectTrans"));
                        $emailService->sendMail($emailSubject, [$order->getUser()->getEmail()], ["orderCompleted" => true, "order" => $order->getNCmd()]);
                        break;
                    case Orders::STATE_HONORED:
                        $order->setValidation($this->translator->trans(Orders::STATE_HONORED, [], "NegasProjectTrans"));
                        $emailService->sendMail($emailSubject, [$order->getUser()->getEmail()], ["orderHonored" => true, "order" => $order->getNCmd()]);
                        break;
                    case Orders::STATE_ABORDED:
                        $order->setValidation($this->translator->trans(Orders::STATE_ABORDED, [], "NegasProjectTrans"));
                        $emailService->sendMail($emailSubject, [$order->getUser()->getEmail()], ["orderAborded" => true, "order" => $order->getNCmd()]);
                        break;
                }
            }
            $em->persist($order);
            $em->flush();
            return $this->redirectToRoute("admin_orders_show", ["id" => $order->getId()]);
        }
        return $this->render("Admin/orders/editStatus.html.twig", [
            "form" => $formStatus->createView(),
            "order" => $order->getNCmd()
        ]);
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
