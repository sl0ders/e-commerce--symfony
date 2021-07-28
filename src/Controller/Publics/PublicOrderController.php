<?php


namespace App\Controller\Publics;

use App\Entity\Orders;
use App\Repository\OrdersRepository;
use App\Services\CartService;
use Knp\Component\Pager\PaginatorInterface;
use Knp\Snappy\Pdf;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
    private TranslatorInterface $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * @Route("/{name}", name="user_orders")
     * @param OrdersRepository $repository
     * @param Request $request
     * @param PaginatorInterface $paginator
     * @return Response
     */
    public function ordersUser(OrdersRepository $repository, Request $request, PaginatorInterface $paginator): Response
    {
        $orders = $repository->findBy([], ["created_at" => "desc"]);
        $pagination = $paginator->paginate(
            $orders, /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            5 /*limit per page*/
        );

        return $this->render('Public/orders/index.html.twig', [
            "pagination" => $pagination
        ]);
    }

    #[Route("/show/{id}", name: "orders_show")]
    public function show(Orders $order): Response
    {
        return $this->render("Public/orders/show.html.twig", [
            "order" => $order
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
                    'order'  => $order
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
