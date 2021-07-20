<?php

namespace App\Controller;

use App\Repository\NewsRepository;
use App\Services\CartService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Contracts\Translation\TranslatorInterface;

class LayoutBundleController extends AbstractController
{

    private TranslatorInterface $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }


    public function leftMenuAction(CartService $cartService): \Symfony\Component\HttpFoundation\Response
    {
        return $this->render('Layout/_sidebar_left.html.twig', []);
    }

    public function rightMenuAction(NewsRepository $repository): \Symfony\Component\HttpFoundation\Response
    {
        return $this->render('Layout/_sidebar_right.html.twig', [
            'news' => $repository->findAll()
        ]);
    }
}
