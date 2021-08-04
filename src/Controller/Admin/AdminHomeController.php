<?php

namespace App\Controller\Admin;

use App\Repository\CompanyRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class AdminHomeController extends AbstractController
{
    /**
     * @Route("/admin/home", name="admin_home")
     */
    public function index(CompanyRepository $companyRepository): Response
    {
        $company = $companyRepository->findOneBy([]);
        return $this->render('Admin/homeAdmin.html.twig', [
            'company' => $company
        ]);
    }
}
