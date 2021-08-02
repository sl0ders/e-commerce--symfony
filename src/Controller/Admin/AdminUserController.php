<?php

namespace App\Controller\Admin;

use App\Datatables\UserDatatable;
use App\Entity\User;
use App\Form\Admin_UserType;
use App\Repository\NotificationRepository;
use App\Repository\UserRepository;
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
 * @Route("/admin/user")
 */
class AdminUserController extends AbstractController
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
        $this->response = $response;
        $this->translator = $translator;
    }

    /**
     * @Route("/", name="admin_user_index", methods={"GET", "POST"})
     * @param UserRepository $userRepository
     * @param Request $request
     * @return Response
     * @throws Exception
     */
    public function index(UserRepository $userRepository, Request $request): Response
    {
        $isAjax = $request->isXmlHttpRequest();
        $datatable = $this->factory->create(UserDatatable::class);
        $datatable->buildDatatable();

        if ($isAjax) {
            $responseService = $this->response;
            $responseService->setDatatable($datatable);
            $responseService->getDatatableQueryBuilder();

            return $responseService->getResponse();
        }

        return $this->render('Admin/user/index.html.twig', [
            'users' => $userRepository->findAll(),
            'datatable' => $datatable
        ]);
    }

    /**
     * @Route("/{id}", name="admin_user_show", methods={"GET","POST"})
     * @param User $user
     * @param Request $request
     * @param NotificationRepository $notificationRepository
     * @return Response
     */
    public function show(User $user, Request $request, NotificationRepository $notificationRepository): Response
    {
        $em = $this->getDoctrine()->getManager();
        if ($request->get("id-notification")) {
            $notification = $notificationRepository->find($request->get("id-notification"));
            $notification->setReadAt(new DateTime());
            $em->persist($notification);
        }
        $formStatus = $this->createForm(Admin_UserType::class, $user);
        $formStatus->handleRequest($request);
        if ($formStatus->isSubmitted() && $formStatus->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            return $this->redirectToRoute("admin_user_index");
        }
        $em->flush();
        return $this->render('Admin/user/show.html.twig', [
            'user' => $user,
        ]);
    }

    /**
     * @Route("/edit/{id}", name="admin_user_editStatus")
     * @param User $user
     * @param Request $request
     * @return Response
     */
    public function editStatus(User $user, Request $request): Response
    {
        $formStatus = $this->createForm(Admin_UserType::class, $user);
        $formStatus->handleRequest($request);
        if ($formStatus->isSubmitted() && $formStatus->isValid()) {
            if ($formStatus->getData()->getStatus() === "Administrateur") {
                $user->setRoles(["ROLE_ADMIN", "ROLE_USER"]);
            } else {
                $user->setRoles(["ROLE_USER"]);
            }
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
        }
        return $this->render('Admin/user/edit.html.twig', [
            "form" => $formStatus->createView()
        ]);
    }

    /**
     * @Route("/{id}", name="admin_user_delete", methods={"DELETE", "POST"})
     * @param Request $request
     * @param User $user
     * @return Response
     */
    public function delete(Request $request, User $user): Response
    {
        if ($this->isCsrfTokenValid('delete' . $user->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($user);
            $entityManager->flush();
        }
        return $this->redirectToRoute('admin_user_index');
    }

    #[Route("/{id}/enabled", name: "admin_user_enabled")]
    public function enable(User $user): RedirectResponse
    {
        if ($user->getEnabled()) {
            $user->setEnabled(false);
            $user->setStatus($this->translator->trans(User::STATE_BAN, [], "NegasProjectTrans"));
        } else {
            $user->setEnabled(true);
            $user->setStatus($this->translator->trans(User::STATE_CLIENT, [], "NegasProjectTrans"));
        }
        $em = $this->getDoctrine()->getManager();
        $em->persist($user);
        $em->flush();
        return $this->redirectToRoute('admin_user_index');
    }
}
