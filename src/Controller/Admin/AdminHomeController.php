<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class AdminHomeController extends AbstractController
{

    private TranslatorInterface $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * @Route("/admin/home", name="admin_home")
     */
    public function index()
    {
        return $this->render('Admin/homeAdmin.html.twig', [
            'controller_name' => 'AdminHomeController',
        ]);
    }
}
