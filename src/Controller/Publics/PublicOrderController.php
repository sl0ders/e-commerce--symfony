<?php


namespace App\Controller\Publics;

use App\Datatables\OrderDatatable;
use App\Entity\Notification;
use App\Entity\Orders;
use App\Entity\User;
use App\Form\OrderChangeStateType;
use App\Repository\NotificationRepository;
use App\Repository\OrdersRepository;
use App\Repository\UserRepository;
use App\Services\CartService;
use App\Services\EmailService;
use App\Services\NotificationServices;
use DateTime;
use Exception;
use Knp\Bundle\SnappyBundle\Snappy\Response\PdfResponse;
use Knp\Component\Pager\PaginatorInterface;
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
 * Class PublicOrderController
 * @package App\Controller\Publics
 * @Route("/orders")
 */
class PublicOrderController extends AbstractController
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
     * @Route("/{id}", name="user_orders")
     * @param Request $request
     * @param User $user
     * @return Response
     * @throws Exception
     */
    public function ordersUser(Request $request, User $user): Response
    {
        $isAjax = $request->isXmlHttpRequest();
        $datatable = $this->factory->create(OrderDatatable::class);
        $datatable->buildDatatable();

        if ($isAjax) {
            $responseService = $this->response;
            $responseService->setDatatable($datatable);
            if ($this->getUser()->getStatus() === "Administrateur") {
                $responseService->getDatatableQueryBuilder();
            } else {
                $datatableQueryBuilder = $responseService->getDatatableQueryBuilder();
                $qb = $datatableQueryBuilder->getQb();
                $qb
                    ->where("orders.user = :user")
                    ->setParameter("user", $user);
            }
            return $responseService->getResponse();
        }

        return $this->render('Public/orders/index.html.twig', [
            "datatable" => $datatable
        ]);
    }

    /**
     * @Route("/pdf-{id}", name="orders_show_pdf", methods={"GET"})
     * @param Orders $order
     * @param Pdf $knpSnappy_Pdf
     * @return PdfResponse
     */
    public function pdfShow(Orders $order, Pdf $knpSnappy_Pdf): PdfResponse
    {
        dd();
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

    #[Route("/show/{id}", name: "orders_show")]
    public function show(Orders $order, Request $request, NotificationRepository $notificationRepository): Response
    {
        $em = $this->getDoctrine()->getManager();
        if ($request->get("id-notification")) {
            $notification = $notificationRepository->find($request->get("id-notification"));
            $notification->setReadAt(new \DateTime());
            $em->persist($notification);
        }
        $em->flush();
        return $this->render("Public/orders/show.html.twig", [
            "order" => $order
        ]);
    }

    /**
     * @throws Exception
     */
    #[Route("/change-state/{id}", name: "order_changeState")]
    public function changeStates(Orders $order, Request $request, NotificationServices $notificationServices, EmailService $emailService, UserRepository $userRepository): RedirectResponse|Response
    {
        $formStatus = $this->createForm(OrderChangeStateType::class, $order);
        $formStatus->handleRequest($request);
        if ($formStatus->isSubmitted() && $formStatus->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $notificationServices->newNotification($this->translator->trans("notification.orders.changeState", ["%orderNumber%" => $order->getNCmd()], "NegasProjectTrans"), [$order->getUser()], ['orders_show', $order->getId()]);
            $subject = $this->translator->trans("email.order.title", ["%orderNumber%" => $order->getNCmd()], "NegasProjectTrans");
            $userAdmin = $userRepository->findByStatus("Administrateur");
            switch ($formStatus->getData()->getValidation()) {
                case $this->translator->trans(Orders::STATE_VALIDATE, [], "NegasProjectTrans"):
                    $emailService->sendMail($subject, [$order->getUser()], ["orderValidate" => true, "order" => $order->getNCmd()]);
                    break;
                case $this->translator->trans(Orders::STATE_COMPLETED, [], "NegasProjectTrans"):
                    $emailService->sendMail($subject, [$order->getUser()], ["orderCompleted" => true, "order" => $order->getNCmd()]);
                    break;
                case $this->translator->trans(Orders::STATE_HONORED, [], "NegasProjectTrans"):
                    $emailService->sendMail($subject, [$order->getUser()], ["orderHonored" => true, "order" => $order->getNCmd()]);
                    break;
                case $this->translator->trans(Orders::STATE_ABORDED, [], "NegasProjectTrans"):
                    $emailService->sendMail($subject, [$order->getUser()], ["orderAborded" => true, "order" => $order->getNCmd()]);
                    $emailService->sendMail($subject, $userAdmin, ["orderAborded" => true, "admin" => true, "order" => $order->getNCmd()]);
                    break;
            }
            $em->persist($order);
            $em->flush();
            return $this->redirectToRoute("orders_show", ["id" => $order->getId()]);
        }
        return $this->render("Admin/orders/editStatus.html.twig", [
            "form" => $formStatus->createView(),
            "order" => $order->getNCmd()
        ]);
    }

    /**
     * @Route("/detail-commande/{id}", name="order_show-pdf")
     * @param Orders $order
     * @param Pdf $knpSnappy_Pdf
     * @return Response
     */
    public function orderShow(Orders $order, Pdf $knpSnappy_Pdf): Response
    {
        $pdfName = $order->getNCmd();
        $knpSnappy_Pdf->generateFromHtml(
            $this->renderView(
                'Public/orders/show.html.twig',
                array(
                    'order' => $order
                )
            ),
            `/path/to/the/$pdfName.pdf`
        );
        return $this->render('Public/orders/show.html.twig', [
            'order' => $order
        ]);
    }

    /**
     * @Route("canceled/{id}", name="public_orders_canceled")
     * @param Orders $order
     * @return Response
     */
    public function canceled(Orders $order): Response
    {
        $em = $this->getDoctrine()->getManager();
        $order->setValidation(5);
        $em->persist($order);
        $em->flush();
        return $this->redirectToRoute('user_orders', ['name' => $this->getUser()->getFirstname()]);
    }
}
