<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class AdminHomeController extends AbstractController
{
    /**
     * @Route("/admin/home", name="admin_home")
     */
    public function index(): Response
    {
        return $this->render('Admin/homeAdmin.html.twig', [
            'controller_name' => 'AdminHomeController',
        ]);
    }
}
