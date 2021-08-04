<?php

namespace App\Controller\Publics;

use App\Datatables\ReportDatatable;
use App\Entity\ReportMessage;
use App\Entity\Report;
use App\Form\ReportMessageType;
use App\Form\ReportType;
use App\Repository\CompanyRepository;
use App\Repository\NotificationRepository;
use App\Repository\UserRepository;
use App\Services\EmailService;
use App\Services\NotificationServices;
use DateTimeImmutable;
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
 * Class ReportController
 * @package App\Controller\User
 * @Route("/report")
 */
class PublicReportController extends AbstractController
{
    private $userRepository, $datatableFactory, $datatableResponse, $companyRepository;

    public function __construct(UserRepository $userRepository, DatatableFactory $datatableFactory, DatatableResponse $datatableResponse, CompanyRepository $companyRepository)
    {
        $this->userRepository = $userRepository;
        $this->datatableFactory = $datatableFactory;
        $this->datatableResponse = $datatableResponse;
        $this->companyRepository = $companyRepository;
    }

    /**
     * @Route("/", name="user_report_index")
     * @param Request $request
     * @return Response
     * @throws Exception
     */
    public function index(Request $request): Response
    {
        $user = $this->userRepository->find($request->get("id"));
        // Variable initialize
        $isAjax = $request->isXmlHttpRequest();
        // Datatable initialize
        $datatable = $this->datatableFactory->create(ReportDatatable::class);
        $datatable->buildDatatable();
        if ($isAjax) {
            $responseService = $this->datatableResponse;
            $responseService->setDatatable($datatable);
            // if the logged user is an administratror, it retrieve all report
            if ($this->container->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
                $responseService->getDatatableQueryBuilder();
            } else {
                // else it retrieve the report related to his company
                $datatableQueryBuilder = $responseService->getDatatableQueryBuilder();
                $qb = $datatableQueryBuilder->getQb();
                $qb
                    ->andWhere("report.user = :user")
                    ->setParameter("user", $user);
            }
            return $responseService->getResponse();
        }
        return $this->render('Public/report/index.html.twig', [
            'datatable' => $datatable
        ]);
    }

    /**
     * @Route("/show/{id}", name="user_report_show")
     * @param Report $report
     * @param Request $request
     * @param NotificationServices $notificationServices
     * @param EmailService $emailService
     * @param TranslatorInterface $translator
     * @return Response
     * @throws Exception
     */
    public function show(Report $report, Request $request, NotificationServices $notificationServices, EmailService $emailService, TranslatorInterface $translator , NotificationRepository $notificationRepository): Response
    {
        $em = $this->getDoctrine()->getManager();
        if ($request->get("id-notification")) {
            $notification = $notificationRepository->find($request->get("id-notification"));
            $notification->setReadAt(new \DateTime());
            $em->persist($notification);
            $em->flush();
        }
        // retrieve the admin company
        $adminCompany = $this->companyRepository->findOneBy([])->getManagers()->toArray();
        $em = $this->getDoctrine()->getManager();
        // create a now report message and the form of this
        $reportMessage = new ReportMessage();
        $form = $this->createForm(ReportMessageType::class, $reportMessage);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $reportMessage
                ->setCreatedAt(new DateTimeImmutable())
                // a set the sender with the company of the logged user
                ->setSender($this->getUser());
            $em->persist($reportMessage);
            // add this message report in the report passed in parameter
            $report->addReportMessage($reportMessage);
            $em->persist($report);
            // creation of an email subject
            $subject = $translator->trans("report.notification.subject", ["%code%" => $report->getReportNumber(), "%user%" => $report->getUser()], "NegasProjectTrans");
            // creation of an message notification
            $message = $translator->trans("report.notification.message", ["%code%" => $report->getReportNumber()], "NegasProjectTrans");
            // if the sender of this message report is the company admin
            if ($reportMessage->getSender()->getStatus() === "Administrateur") {
                // i change the order of sender and receiver of notification and the success message same for the mail
                $notificationServices->newNotification($message, [$report->getUser()], ["user_report_show", $report->getId()]);
                $this->addFlash("success", "flash.report.ReportMessage.createdSuccessfullyAtClient");
                $emailService->sendMail($subject, [$report->getUser()], [
                    "reportSendMail" => true,
                    "report" => $report,
                    "message" => $message
                ]);
            } else {
                $notificationServices->newNotification($message, $adminCompany,["user_report_show", $report->getId()]);
                $this->addFlash("success", "flash.report.ReportMessage.createdSuccessfullyAtAdmin");
                $emailService->sendMail($subject, $adminCompany, [
                    "reportSendMail" => true,
                    "report" => $report,
                    "message" => $message
                ]);
            }
            $em->flush();
            return $this->redirectToRoute("user_report_show", ['id' => $report->getId()]);
        }

            return $this->render("Public/report/show.html.twig", [
            "report" => $report,
            "form" => $form->createView()
        ]);
    }

    /**
     * each report can be archived
     * @Route("/enabled/{id}", name="user_report_enabled")
     * @param Report $report
     * @param EntityManagerInterface $em
     * @param TranslatorInterface $translator
     * @return RedirectResponse
     */
    public function enabled(Report $report, EntityManagerInterface $em, TranslatorInterface $translator): RedirectResponse
    {
        if ($report->getStatus() === true) {
            $report->setStatus(false);
            $message = $translator->trans("flash.report.disabled", [], 'FlashesMessages');
        } else {
            $report->setStatus(true);
            $message = $translator->trans("flash.report.enabled", [], 'FlashesMessages');
        }
        $em->persist($report);
        $em->flush();
        $this->addFlash("success", $message);
        return $this->redirect($_SERVER['HTTP_REFERER']);
    }
}
