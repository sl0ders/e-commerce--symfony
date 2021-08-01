<?php

namespace App\Controller\Admin;

use App\Datatables\ContactDatatable;
use App\Entity\Contact;
use App\Repository\ContactRepository;
use Sg\DatatablesBundle\Datatable\DatatableFactory;
use Sg\DatatablesBundle\Response\DatatableResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;


/**
 * @Route("/admin/contact")
 */
class AdminContactController extends AbstractController
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
     * @Route("/", name="admin_contact_index", methods={"GET"})
     * @param ContactRepository $contactRepository
     * @return Response
     * @throws \Exception
     */
    public function index(ContactRepository $contactRepository, Request $request): Response
    {
        $isAjax = $request->isXmlHttpRequest();
        $datatable = $this->factory->create(ContactDatatable::class);
        $datatable->buildDatatable();

        if ($isAjax) {
            $responseService = $this->response;
            $responseService->setDatatable($datatable);
            $responseService->getDatatableQueryBuilder();

            return $responseService->getResponse();
        }
        return $this->render('Admin/contact/index.html.twig', [
            "datatable" => $datatable
        ]);
    }


    /**
     * @Route("/{id}", name="admin_contact_show", methods={"GET"})
     * @param Contact $contact
     * @return Response
     */
    public function show(Contact $contact): Response
    {
        return $this->render('Admin/contact/show.html.twig', [
            'contact' => $contact,
        ]);
    }

    /**
     * @Route("/{id}", name="admin_contact_delete", methods={"DELETE", "POST"})
     * @param Request $request
     * @param Contact $contact
     * @return Response
     */
    public function delete(Request $request, Contact $contact): Response
    {
        if ($this->isCsrfTokenValid('delete' . $contact->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($contact);
            $entityManager->flush();
            // display of a success message
            $this->addFlash("success", "flash.contact.deleteSuccessfully");
        }
        return $this->redirectToRoute('admin_contact_index');
    }
}
