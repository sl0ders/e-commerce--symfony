<?php

namespace App\Controller;

use App\Entity\Orders;
use App\Services\CartService;
use App\Services\EmailService;
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
     * @return RedirectResponse
     * @throws Exception
     */
    public function validCart(EmailService $email): RedirectResponse
    {
        $productsQuantity = [];
        $em = $this->getDoctrine()->getManager();
        $carts = $this->cartService->getAllCart();
        $subject = $this->translator->trans("email.add_new_order", [], "NegasProjectTrans");
        $order = new Orders();
        $order->setUser($this->getUser());
        $order->setCreatedAt(new DateTime());
        $order->setNCmd(date("Y-d-m-i-s"));
        $order->setTotal(intval($this->cartService->getTotal()));
        $order->setValidation($this->translator->trans($order::STATE_IN_COURSE, [], "NegasProjectTrans"));
        foreach ($carts as $cart) {
            $order->addProduct($cart['product']);
            $productQuantity["product"] = $cart["product"];
            $productQuantity["quantity"] = $cart["quantity"];
            array_push($productsQuantity, $productQuantity);
        }
        $em->persist($order);
        $em->flush();
        $email->sendMail($subject, [$this->getUser()->getEmail()], ["order_in_course" => true, "order" => $order, "productsQuantity" => $productsQuantity]);
        $this->get('session')->remove('cart');
        return $this->redirectToRoute("home");
    }
}
