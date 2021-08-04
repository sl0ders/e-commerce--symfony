<?php

namespace App\Controller\Admin;

use App\Datatables\ProductDatatable;
use App\Entity\News;
use App\Entity\Package;
use App\Entity\Product;
use App\Entity\Stock;
use App\Form\ProductType;
use App\Repository\ProductRepository;
use DateTime;
use Exception;
use Sg\DatatablesBundle\Datatable\DatatableFactory;
use Sg\DatatablesBundle\Response\DatatableResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @Route("/admin/product")
 */
class AdminProductController extends AbstractController
{
    /**
     * @var DatatableFactory
     */
    private DatatableFactory $factory;

    /**
     * @var DatatableResponse
     */
    private DatatableResponse $response;
    private TranslatorInterface $translator;

    public function __construct(DatatableFactory $factory, DatatableResponse $response, TranslatorInterface $translator)
    {
        $this->factory = $factory;
        $this->response = $response;
        $this->translator = $translator;
    }

    /**
     * @Route("/", name="admin_product_index", methods={"GET"})
     * @param ProductRepository $productRepository
     * @param Request $request
     * @return Response
     * @throws Exception
     */
    public function index(ProductRepository $productRepository, Request $request): Response
    {
        $isAjax = $request->isXmlHttpRequest();
        $datatable = $this->factory->create(ProductDatatable::class);
        $datatable->buildDatatable();

        if ($isAjax) {
            $responseService = $this->response;
            $responseService->setDatatable($datatable);
            $responseService->getDatatableQueryBuilder();

            return $responseService->getResponse();
        }
        return $this->render('Admin/product/index.html.twig', [
            'products' => $productRepository->findAll(),
            "datatable" => $datatable
        ]);
    }

    /**
     * @Route("/new", name="admin_product_new", methods={"GET","POST"})
     * @param Request $request
     * @return Response
     * @throws Exception
     */
    public function new(Request $request): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $product = new Product();
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($form->get("conditioning")->getData() != null) {
                $package = new Package();
                $package->setConditioning($form->get("conditioning")->getData());
                $package->setQuantity($form->get("packageValue")->getData());
                $package->setUnity($form->get("unity")->getData());
            } else {
                $package = $form->get("package")->getData();
            }
            $entityManager->persist($package);
            $stock = new Stock();
            $stock->setProduct($product);
            $stock->setQuantity($form->get("quantity")->getData());
            $stock->setMajAt(new DateTime());
            $product->setUpdatedAt(new DateTime());
            $product->setFilenameJpg(strtolower($form->getData()->getPictureFiles()[0]->getClientOriginalName()));
            $product->setFilenamePng(strtolower($form->getData()->getPictureFilesPng()[0]->getClientOriginalName()));
            $product->setPackage($package);
            $news = new News();
            $news->setProduct($product);
            $news->setCreatedAt(new DateTime());
            $news->setTitle($this->translator->trans("news.product.arrival", ["%product%" => $product->getName()], "NegasProjectTrans"));
            $news->setEnabled(true);
            $entityManager->persist($news);
            $entityManager->persist($product);
            $entityManager->persist($stock);
            $entityManager->flush();
            $this->addFlash("success", "flash.product.createSuccessFully");
            return $this->redirectToRoute('admin_product_index');
        }

        return $this->render('Admin/product/new.html.twig', [
            'product' => $product,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="admin_product_show", methods={"GET"})
     * @param Product $product
     * @return Response
     */
    public function show(Product $product): Response
    {
        return $this->render('Admin/product/show.html.twig', [
            'product' => $product,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="admin_product_edit", methods={"GET","POST"})
     * @param Request $request
     * @param Product $product
     * @return Response
     */
    public function edit(Request $request, Product $product): Response
    {
        $em = $this->getDoctrine()->getManager();
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($form->get("conditioning")->getData() != null) {
                $package = new Package();
                $package->setConditioning($form->get("conditioning")->getData());
                $package->setQuantity($form->get("packageValue")->getData());
                $package->setUnity($form->get("unity")->getData());
            } else {
                $package = $form->get("package")->getData();
            }
            $em->persist($package);
            if (!empty($form->getData()->getPictureFiles())) {
                $product->setFilenameJpg($form->getData()->getPictureFiles()[0]->getClientOriginalName());
            }
            if (!empty($form->getData()->getPictureFilesPng())) {
                $product->setFilenamePng($form->getData()->getPictureFilesPng()[0]->getClientOriginalName());
            }
            $product->setPackage($package);
            $em->persist($product);
            $em->flush();
            return $this->redirectToRoute('admin_product_index');
        }

        return $this->render('Admin/product/edit.html.twig', [
            'product' => $product,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="admin_product_delete")
     * @param Request $request
     * @param Product $product
     * @return Response
     */
    public
    function delete(Request $request, Product $product): Response
    {
        if ($this->isCsrfTokenValid('delete' . $product->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($product);
            $entityManager->flush();
        }

        return $this->redirectToRoute('admin_product_index');
    }

    /**
     * @Route("enabled/{id}", name="admin_product_enabled")
     */
    public
    function enabled(Product $product): RedirectResponse
    {
        $em = $this->getDoctrine()->getManager();
        if ($product->getEnabled() == true) {
            $product->setEnabled(false);
        } else {
            $product->setEnabled(true);
        }
        $em->persist($product);
        $em->flush();
        return $this->redirectToRoute("admin_product_index");
    }
}
