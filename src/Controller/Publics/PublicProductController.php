<?php

namespace App\Controller\Publics;

use App\Entity\Product;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class PublicProductController extends AbstractController
{
    private TranslatorInterface $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * @Route("/public/product/{id}", name="public_product_show")
     * @param Product $product
     * @return Response
     */
    public function show(Product $product): Response
    {
        return $this->render('public/product/show.html.twig', [
            'product' => $product,
        ]);
    }
}
