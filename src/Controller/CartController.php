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

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
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
        return $this->redirectToRoute("home",
            [
                "productId" => $id,
                "productQuantity" => $quantity
            ]);
    }

    #[Route("/remove/cart-item", name: "remove_cart_idem")]
    public function removeCartItem()
    {

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
        $orders = [];
        $em = $this->getDoctrine()->getManager();
        $carts = $this->cartService->getAllCart();
        foreach ($carts as $cart) {
            $order = new Orders();
            $order
                ->setCreatedAt(new DateTime())
                ->setNCmd(date("Y-d-m-i-s"))
                ->setQuantity($cart['quantity'])
                ->setUser($this->getUser())
                ->setProducts($cart['product'])
                ->setTotal(intval($this->cartService->getTotal()))
                ->setValidation(1);
            $em->persist($order);
            array_push($orders, $order);
        }
        $email->sendMail("Commande en cour de traitement", [$this->getUser()], ["order_in_course" => true, "orders" => $orders]);
        $em->flush();
        $this->get('session')->remove('cart');
        return $this->redirectToRoute("home");
    }
}
