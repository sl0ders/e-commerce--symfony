<?php


namespace App\Controller\Publics;


use App\Entity\Contact;
use App\Form\ContactType;
use App\Repository\UserRepository;
use App\Services\EmailService;
use App\Services\NotificationServices;
use DateTime;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;


#[Route("/contact")]
class PublicContactController extends AbstractController
{
    private TranslatorInterface $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * @Route("/new", name="public_contact_new", methods={"GET","POST"})
     * @param Request $request
     * @param EmailService $emailService
     * @param NotificationServices $notificationServices
     * @return Response
     */
    public function new(Request $request, EmailService $emailService, NotificationServices $notificationServices, UserRepository $userRepository): Response
    {
        $contact = new Contact();
        $form = $this->createForm(ContactType::class, $contact);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $usersAdmin = $userRepository->findByStatus("Administrateur");
            $subject = $this->translator->trans("email.contact.new_admin", [], "NegasProjectTrans");
            $entityManager = $this->getDoctrine()->getManager();
            $contact->setEmail($this->getUser()->getEmail())
            ->setFirstname($this->getUser()->getFirstname())
            ->setCreatedAt(new DateTime())
            ->setUser($this->getUser());
            $emailService->sendMail($subject,$usersAdmin, ['new_contact' => true, 'client' => $this->getUser()]);
            $entityManager->persist($contact);
            $entityManager->flush();
            $notificationServices->newNotification($this->translator->trans("notification.contact.new", ["%user%" => $contact->getUser()->getFullname()], "NegasProjectTrans"), $usersAdmin, ["admin_contact_show", $contact->getId()] );
            $entityManager->flush();
            $this->addFlash("success", "flash.contact.createSuccessfully");
            return $this->redirectToRoute('home');
        }

        return $this->render('Public/contact/new.html.twig', [
            'contact' => $contact,
            'form' => $form->createView(),
        ]);
    }
}
