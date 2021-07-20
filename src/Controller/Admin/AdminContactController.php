<?php

namespace App\Controller\Admin;

use App\Entity\Contact;
use App\Repository\ContactRepository;
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

    private TranslatorInterface $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * @Route("/", name="admin_contact_index", methods={"GET"})
     * @param ContactRepository $contactRepository
     * @return Response
     */
    public function index(ContactRepository $contactRepository): Response
    {
        return $this->render('Admin/contact/index.html.twig', [
            'contacts' => $contactRepository->findAll(),
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
     * @Route("/{id}", name="admin_contact_delete", methods={"DELETE"})
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
        }

        return $this->redirectToRoute('admin_contact_index');
    }
}
