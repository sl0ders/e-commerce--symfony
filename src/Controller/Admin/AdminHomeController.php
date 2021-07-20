<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class AdminHomeController extends AbstractController
{
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
