<?php

namespace App\Controller\Admin;

use App\Entity\Company;
use App\Form\CompanyType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route("/admin/company")]
class AdminCompanyController extends AbstractController
{
    #[Route('/{id}', name: 'admin_company_show')]
    public function show(Company $company): Response
    {
        return $this->render('Admin/company/show.html.twig', [
            'company' => $company,
        ]);
    }

    #[Route('/edit/{id}', name: 'admin_company_edit')]
    public function edit(Company $company, Request $request): Response
    {
        $form = $this->createForm(CompanyType::class, $company);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($company);
            $em->flush();
        }
        return $this->render('Admin/company/edit.html.twig', [
            'company' => $company,
            'form' => $form->createView()
        ]);
    }
}
