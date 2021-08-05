<?php

namespace App\Controller\Publics;

use App\Entity\News;
use App\Entity\Package;
use App\Entity\Product;
use App\Entity\Report;
use App\Entity\Stock;
use App\Entity\User;
use App\Form\ProductType;
use App\Form\Public_UserType;
use App\Form\ReportType;
use App\Repository\ProductRepository;
use App\Services\AddProductService;
use App\Services\CartService;
use App\Services\ReportCodeGenerator;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PublicHomeController extends AbstractController
{

    /**
     * @var CartService
     */
    private $cartService;
    private ReportCodeGenerator $codeGenerator;

    public function __construct(CartService $cartService, ReportCodeGenerator $codeGenerator)
    {
        $this->cartService = $cartService;
        $this->codeGenerator = $codeGenerator;
    }

    /**
     * @Route("/", name="home")
     * @param ProductRepository $productRepository
     * @param Request $request
     * @return Response
     */
    public function index(ProductRepository $productRepository, Request $request, AddProductService $addProductService): Response
    {
        $em = $this->getDoctrine()->getManager();
        $product = new Product();
        $form = $this->createForm(ProductType::class, $product);
        $news = new News();
        $stock = new Stock();
        $addProductService->addProduct($product, $news, $stock, $form, $request);
        if ($form->isSubmitted() && $form->isValid()) {
            return $this->redirectToRoute('admin_product_index');
        }
        $report = new Report();
        $formReport = $this->createForm(ReportType::class, $report);
        $formReport->handleRequest($request);
        if ($formReport->isSubmitted() && $formReport->isValid()) {
            $report
                ->setCreatedAt(new \DateTimeImmutable())
                ->setReportNumber($this->codeGenerator->generate($report))
                ->setStatus(true)
                ->setUser($this->getUser());
            $em->persist($report);
            $em->flush();
            return $this->redirectToRoute("user_report_show", ["id" => $report->getId()]);
        }

        return $this->render('Public/homePublic.html.twig', [
            'cartProducts' => $this->cartService->getAllCart(),
            'cartTotal' => $this->cartService->getTotal(),
            'products' => $productRepository->findBy(["enabled" => true]),
            "form" => $form->createView(),
            "formReport" => $formReport->createView()
        ]);
    }

    /**
     * @Route("/{id}/mon-profile", name="home_user")
     * @return Response
     */
    public function homeUser(): Response
    {
        $user = $this->getUser();
        return $this->render('Public/user/home.html.twig', [
            'user' => $user
        ]);
    }

    /**
     * @Route("/{id}/détail-profil", name="user_profile_detail")
     * @return Response
     */
    public function ProfileUser(): Response
    {
        $user = $this->getUser();
        return $this->render('Public/user/show.html.twig', [
            'user' => $user
        ]);
    }


    /**
     * @Route("/{id}/édit-profil", name="public_edit_user")
     * @param User $user
     * @param Request $request
     * @return Response
     */
    public function editUser(User $user, Request $request): Response
    {
        $form = $this->createForm(Public_UserType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
            return $this->redirectToRoute('user_profile_detail', ['id' => $user->getId()]);
        }
        return $this->render('Public/user/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
