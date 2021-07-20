<?php

namespace App\Controller\Publics;

use App\Entity\Product;
use App\Entity\Stock;
use App\Entity\User;
use App\Form\ProductType;
use App\Form\Public_UserType;
use App\Repository\ProductRepository;
use App\Services\CartService;
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

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    /**
     * @Route("/", name="home")
     * @param ProductRepository $productRepository
     * @param Request $request
     * @return Response
     */
    public function index(ProductRepository $productRepository, Request $request): Response
    {
        $product = new Product();
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $stock = new Stock();
            $stock->setProduct($product);
            $stock->setQuantity($product->getQuantity());
            $stock->setMajAt(new DateTime());
            $entityManager = $this->getDoctrine()->getManager();
            $product->setUpdatedAt(new DateTime());
            $product->setFilenameJpg(strtolower($form->getData()->getPictureFiles()[0]->getClientOriginalName()));
            $product->setFilenamePng(strtolower($form->getData()->getPictureFilesPng()[0]->getClientOriginalName()));
            $entityManager->persist($product);
            $entityManager->persist($stock);
            $entityManager->flush();
            return $this->redirectToRoute('admin_product_index');
        }
        return $this->render('Public/homePublic.html.twig', [
            'cartProducts' => $this->cartService->getAllCart(),
            'cartTotal' => $this->cartService->getTotal(),
            'products' => $productRepository->findBy(["enabled" => true]),
            "form" => $form->createView()
        ]);
    }

    /**
     * @Route("/{name}/mon-profile", name="home_user")
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
     * @Route("/{name}/détail-profil", name="user_profile_detail")
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
     * @Route("/{name}/édit-profil", name="public_edit_user")
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
            return $this->redirectToRoute('user_profile_detail', ['name' => $user->getName()]);
        }
        return $this->render('Public/user/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
