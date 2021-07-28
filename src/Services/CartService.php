<?php

namespace App\Services;

use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class CartService
{
    private $session;
    private $productRepository;

    public function __construct(SessionInterface $session, ProductRepository $productRepository)
    {
        $this->session = $session;
        $this->productRepository = $productRepository;
    }

    public function add($id, $quantity)
    {
        $cart = $this->session->get('cart', []);

        if (!empty($cart[$id])) {
            $cart[$id] += $quantity;
        } else {
            $cart[$id] = $quantity;
        }
        $this->session->set('cart', $cart);
    }

    public function refresh($id, $quantity)
    {
        $cart = $this->session->get('cart', []);
        $cart[$id] = $quantity;
        $this->session->set('cart', $cart);
    }

    public function getAllCart(): array
    {
        $cart = $this->session->get('cart', []);
        $cartWithData = [];
        foreach ($cart as $id => $quantity) {
            $product = $this->productRepository->find($id);

            $cartWithData[] = [
                'product' => $product,
                'quantity' => $quantity
            ];
        }
        return $cartWithData;
    }

    public function getTotal()
    {
        $total = 0;
        foreach ($this->getAllCart() as $item) {
            if ($item['product'] != null) {
                $totalItem = $item['product']->getPrice() * $item['quantity'];
                $total += $totalItem;
            }
        }
        return $total;
    }
}
