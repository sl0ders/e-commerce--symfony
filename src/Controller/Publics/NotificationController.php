<?php

namespace App\Controller\Publics;

use App\Datatables\NotificationDatatable;
use App\Entity\Notification;
use App\Repository\NotificationRepository;
use App\Repository\UserRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
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
 * @Route("/parameters/notification")
 */
class NotificationController extends AbstractController
{
    /**
     * @var DatatableFactory|UserRepository
     */
    private UserRepository|DatatableFactory $userRepository;
    private DatatableFactory $datatableFactory;
    private DatatableResponse|DatatableFactory $datatableResponse;
    /**
     * @var TranslatorInterface
     */
    private TranslatorInterface $translator;

    public function __construct(
        DatatableFactory $datatableFactory,
        DatatableResponse $datatableResponse,
        UserRepository $userRepository,
        TranslatorInterface $translator
    )
    {
        $this->datatableFactory = $datatableFactory;
        $this->datatableResponse = $datatableResponse;
        $this->userRepository = $userRepository;
        $this->translator = $translator;
    }

    /**
     * @Route("/", name="user_notification_index", methods={"GET"})
     * @param Request $request
     * @return Response
     * @throws Exception
     */
    public function index(Request $request, NotificationRepository $notificationRepository): Response
    {
        $user = $this->userRepository->find($this->getUser());
        // Variable initialize
        $isAjax = $request->isXmlHttpRequest();
        // Datatable initialize
        $datatable = $this->datatableFactory->create(NotificationDatatable::class);
        $datatable->buildDatatable();
        if ($isAjax) {
            $responseService = $this->datatableResponse;
            $responseService->setDatatable($datatable);
            // if the person connected is an administrator he will see all the notifications of all the companies
            if ($this->container->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
                $responseService->getDatatableQueryBuilder();
            } else {
                //otherwise he will only see notifications that concern his own company
                $datatableQueryBuilder = $responseService->getDatatableQueryBuilder();
                $qb = $datatableQueryBuilder->getQb();
                $qb
                    ->leftJoin("notification.receiver", "company")
                    ->andWhere($qb->expr()->eq('company', ':q_company'))
                    ->setParameter("q_company", $user->getCompany());
            }
            return $responseService->getResponse();
        }
        return $this->render('Public/notification/index.html.twig', [
            'notifications' => $notificationRepository->findByUser($user),
            'datatable' => $datatable
        ]);
    }

    /**
     * @Route("/{id}", name="user_notification_show", methods={"GET"})
     * @param Notification $notification
     * @return Response
     */
    public function show(Notification $notification): Response
    {
        $em = $this->getDoctrine()->getManager();
        $notification->setReadAt(new DateTime());
        $em->persist($notification);
        $em->flush();
        return $this->render('Public/notification/show.html.twig', [
            'notification' => $notification,
        ]);
    }

    /**
     * this function allows to pass a notification of the status archiver to visible
     * @Route("/enabled/{id}", name="user_notification_enabled")
     * @param Notification $notification
     * @param EntityManagerInterface $em
     * @param TranslatorInterface $translator
     * @return RedirectResponse
     */
    public function enabled(Notification $notification, EntityManagerInterface $em, TranslatorInterface $translator): RedirectResponse
    {
        if ($notification->getIsEnabled() === true) {
            $notification->setIsEnabled(false);
//            $message = $translator->trans("flash.notification.disabled", [], 'FlashesMessages');
        } else {
            $notification->setIsEnabled(true);
//            $message = $translator->trans("flash.notification.enabled", [], 'FlashesMessages');
        }
        $em->persist($notification);
        $em->flush();
//        $this->addFlash("success", $message);
        // $_SERVER['HTTP_REFERER'] returns the user to the previously visited page
        return $this->redirect($_SERVER['HTTP_REFERER']);
    }
}
