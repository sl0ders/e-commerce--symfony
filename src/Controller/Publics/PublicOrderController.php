<?php


namespace App\Controller\Publics;

use App\Entity\Orders;
use App\Repository\OrdersRepository;
use App\Services\CartService;
use ContainerMMNFAvx\getKnpSnappy_PdfService;
use DateTime;
use Knp\Snappy\Pdf;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Config\KnpSnappyConfig;

/**
 * Class PublicOrderController
 * @package App\Controller\Publics
 * @Route("/commande")
 */
class PublicOrderController extends AbstractController
{
    /**
     * @Route("/{name}", name="user_orders")
     * @param OrdersRepository $repository
     * @param CartService $cartService
     * @return Response
     */
    public function ordersUser(OrdersRepository $repository, CartService $cartService): Response
    {
        $cartService->getAllCart();
        $orders = $repository->findAll();
        return $this->render('Public/orders/index.html.twig', [
            "orders" => $orders
        ]);
    }

    /**
     * @Route("/detail-commande/{id}", name="order_show")
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

    #[Route("/generate-pdf", name: "admin_order_generate")]
    public function generatePdf(Pdf $knpSnappy_Pdf) {

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
