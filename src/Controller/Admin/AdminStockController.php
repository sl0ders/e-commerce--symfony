<?php

namespace App\Controller\Admin;

use App\Entity\Stock;
use App\Form\StockType;
use App\Repository\StockRepository;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @Route("/admin/stock")
 */
class AdminStockController extends AbstractController
{
    /**
     * @Route("/", name="admin_stock_index", methods={"GET"})
     * @param StockRepository $stockRepository
     * @return Response
     */
    public function index(StockRepository $stockRepository): Response
    {
        return $this->render('Admin/stock/index.html.twig', [
            'stocks' => $stockRepository->findAll(),
        ]);
    }

    /**
     * @Route("/{id}/edit", name="admin_stock_edit", methods={"GET","POST"})
     * @param Request $request
     * @param Stock $stock
     * @return Response
     */
    public function edit(Request $request, Stock $stock): Response
    {
        $quantity = $request->request->get("value");
        $stock->setQuantity($quantity);
        $stock->setMajAt(new DateTime());
        $em = $this->getDoctrine()->getManager();
        $em->persist($stock);
        $em->flush();
        $response = new Response(json_encode($quantity));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    /**
     * @Route("/{id}", name="admin_stock_delete", methods={"DELETE", "POST"})
     * @param Request $request
     * @param Stock $stock
     * @return Response
     */
    public function delete(Request $request, Stock $stock): Response
    {
        if ($this->isCsrfTokenValid('delete' . $stock->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($stock);
            $entityManager->flush();
        }

        return $this->redirectToRoute('admin_stock_index');
    }
}
