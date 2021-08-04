<?php


namespace App\Controller\Admin;

use App\Datatables\PackagingDatatable;
use App\Entity\Package;
use App\Entity\Product;
use App\Form\PackageType;
use App\Repository\ProductRepository;
use Exception;
use Sg\DatatablesBundle\Datatable\DatatableFactory;
use Sg\DatatablesBundle\Response\DatatableResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route("/admin/package")]
class AdminPackageController extends AbstractController
{

    /**
     * @var DatatableFactory
     */
    private DatatableFactory $factory;

    /**
     * @var DatatableResponse
     */
    private DatatableResponse $response;

    public function __construct(DatatableFactory $factory, DatatableResponse $response, TranslatorInterface $translator)
    {
        $this->factory = $factory;
        $this->translator = $translator;
        $this->response = $response;
    }


    /**
     * @throws Exception
     */
    #[Route('/', name: 'admin_package_index')]
    public function index(Request $request): JsonResponse|Response
    {
        $isAjax = $request->isXmlHttpRequest();
        $datatable = $this->factory->create(PackagingDatatable::class);
        $datatable->buildDatatable();

        if ($isAjax) {
            $responseService = $this->response;
            $responseService->setDatatable($datatable);
            $responseService->getDatatableQueryBuilder();

            return $responseService->getResponse();
        }
        return $this->render("Admin/package/index.html.twig", [
            "datatable" => $datatable
        ]);
    }

    #[Route("/new", name: 'admin_package_new')]
    public function new(Request $request): RedirectResponse|Response
    {
        $package = new Package();
        $form = $this->createForm(PackageType::class, $package);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($package);
            $em->flush();
            $this->addFlash("success", "flash.package.createSuccessfully");
            return $this->redirectToRoute("admin_package_index");
        }
        return $this->render("Admin/package/new.html.twig", [
            "form" => $form->createView()
        ]);
    }

    #[Route("/edit/{id}", name: "admin_package_edit")]
    public function edit(Package $package, Request $request) {
        $form = $this->createForm(PackageType::class, $package);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($package);
            $em->flush();
            $this->addFlash("success", "flash.package.editSuccessfully");
           return $this->redirectToRoute("admin_package_index");
        }
       return $this->render("Admin/package/edit.html.twig", [
            "form" => $form->createView(),
            "package" => $package
        ]);
    }

    #[Route("/delete/{id}", name: "admin_package_delete", methods: ["DELETE", "GET"])]
    public function delete(ProductRepository $productRepository, Package $package): RedirectResponse
    {
        $product = $productRepository->findBy(["package" => $package]);
        if (isset($product)) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($package);
            $em->flush();
            $this->addFlash("success", "flash.package.deleteSuccessfully");
        } else {
            $this->addFlash("danger", "flash.package.deleteCancelled");
        }
        return $this->redirectToRoute('admin_package_index');
    }
}
