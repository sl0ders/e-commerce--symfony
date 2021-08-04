<?php

namespace App\Controller;

use App\Entity\LinkOrderProduct;
use App\Entity\Orders;
use App\Entity\User;
use App\Repository\CompanyRepository;
use App\Repository\LinkOrderProductRepository;
use App\Repository\ProductRepository;
use App\Repository\StockRepository;
use App\Repository\UserRepository;
use App\Services\CartService;
use App\Services\EmailService;
use App\Services\NotificationServices;
use DateTime;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class CartController
 * @package App\Controller
 * @Route("/cart")
 */
class CartController extends AbstractController
{
    /**
     * @var CartService
     */
    private CartService $cartService;
    private EmailService $emailService;
    private TranslatorInterface $translator;

    public function __construct(CartService $cartService, TranslatorInterface $translator, EmailService $emailService)
    {
        $this->cartService = $cartService;
        $this->translator = $translator;
        $this->emailService = $emailService;
    }

    /**
     * @Route("/add/{id}",name="cart_add")
     * @param $id
     * @return RedirectResponse
     */
    public function add($id): RedirectResponse
    {
        $quantity = $_POST["quantity"];
        $this->cartService->add($id, $quantity);
        return $this->redirectToRoute("home", [
            "productId" => $id,
            "productQuantity" => $quantity
        ]);
    }

    /**
     * @Route("/remove/{id}", name="cart_remove")
     * @param $id
     * @return RedirectResponse
     */
    public function refresh($id): RedirectResponse
    {
        $quantity = $_POST["quantity"];
        $this->cartService->refresh($id, $quantity);
        return $this->redirectToRoute("home");
    }

    /**
     * @Route("/valid", name="cart_valid")
     * @param EmailService $email
     * @param StockRepository $stockRepository
     * @param NotificationServices $notificationServices
     * @param CompanyRepository $companyRepository
     * @return RedirectResponse
     * @throws Exception
     */
    public function validCart(EmailService         $email,
                              StockRepository      $stockRepository,
                              NotificationServices $notificationServices,
                              CompanyRepository $companyRepository,
    ): RedirectResponse
    {
        $company = $companyRepository->findOneBy([]);
        $productsQuantity = [];
        $em = $this->getDoctrine()->getManager();
        $carts = $this->cartService->getAllCart();
        $subjectUser = $this->translator->trans("email.add_new_order", [], "NegasProjectTrans");
        $subjectAdmin = $this->translator->trans("email.add_new_order_admin", [], "NegasProjectTrans");
        $order = new Orders();
        $order->setUser($this->getUser());
        $order->setCreatedAt(new DateTime());
        $order->setNCmd(date("Y-d-m-i-s"));
        $order->setTotal($this->cartService->getTotal());
        $order->setValidation($this->translator->trans($order::STATE_IN_COURSE, [], "NegasProjectTrans"));
        $userAdmin = $company->getManagers()->toArray();
        foreach ($carts as $cart) {
            if ($cart['quantity'] > 0) {
                $linkOrderProduct = new LinkOrderProduct();
                $linkOrderProduct->setOrders($order);
                $linkOrderProduct->setProduct($cart["product"]);
                $linkOrderProduct->setQuantity($cart["quantity"]);
                $em->persist($linkOrderProduct);
                $productQuantity["product"] = $cart["product"];
                $productQuantity["quantity"] = $cart["quantity"];
                $stock = $stockRepository->findOneBy(['product' => $productQuantity["product"]]);
                $stock->setQuantity($stock->getQuantity() - $productQuantity["quantity"]);
                $stock->setMajAt(new DateTime());
                $em->persist($stock);
                if ($stock->getQuantity() < 20) {
                    $subjectStockAdmin = $this->translator->trans("email.subject.stock_report", [], "NegasProjectTrans");
                    $this->emailService->sendMail($subjectStockAdmin, $userAdmin, ["productQuantity"=> $productQuantity, "stockReport" => true]);
                }
                array_push($productsQuantity, $productQuantity);
            }
        }
        $this->addFlash("success", "flash.order.addedSuccessfully");
        $user = $this->getUser()->getFullname();
        $em->persist($order);
        $em->flush();
        $notificationServices->newNotification($this->translator->trans("notification.orders.new", ["%user%" => $user], "NegasProjectTrans"), $userAdmin, ["orders_show", $order->getId()]);
        $email->sendMail($subjectUser, [$this->getUser(),], ["order_in_course" => true,"users" => true, "order" => $order, "productsQuantity" => $productsQuantity]);
        $email->sendMail($subjectAdmin, $userAdmin, ["order_in_course_admin" => true, "order_in_course" => true, "order" => $order, "user" => $this->getUser(), "productsQuantity" => $productsQuantity]);
        $em->flush();
        $this->get('session')->remove('cart');
        return $this->redirectToRoute("home");
    }
}
