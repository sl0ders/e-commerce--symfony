<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use App\Services\EmailService;
use App\Services\NotificationServices;
use DateTime;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Contracts\Translation\TranslatorInterface;

class SecurityController extends AbstractController
{

    function genererChaineAleatoire($longueur, $listeCar = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ')
    {
        $chaine = '';
        $max = mb_strlen($listeCar, '8bit') - 1;
        for ($i = 0; $i < $longueur; ++$i) {
            $chaine .= $listeCar[random_int(0, $max)];
        }
        return $chaine;
    }

    /**
     * @var TranslatorInterface
     */
    private TranslatorInterface $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * @Route("/login", name="app_login")
     * @param AuthenticationUtils $authenticationUtils
     * @return Response
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
         if ($this->getUser()) {
             return $this->redirectToRoute('home');
         }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    /**
     * @Route("/signin", name="app_signin")
     * @param Request $request
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @return RedirectResponse|Response
     * @throws Exception
     */
    public function add(Request $request, UserPasswordEncoderInterface $passwordEncoder, EmailService $emailService, NotificationServices $notificationServices, UserRepository $userRepository): RedirectResponse|Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $user->setStatus($this->translator->trans(User::STATE_VISITOR, [], "NegasProjectTrans"));
            $user->setRoles(['ROLE_USER']);
            $user->setCreatedAt(new DateTime());
            $user->setPassword($passwordEncoder->encodePassword($user, $user->getPassword()));
            $user->setEnabled(1);
            $user->setEmailCode($this->genererChaineAleatoire(20));
            $em->persist($user);
            $em->flush();
            $emailSubjectAdmin = $this->translator->trans("email.user.add_new_user", [], "NegasProjectTrans");
            $emailSubjectUser = $this->translator->trans("email.user.confirme_email", [], "NegasProjectTrans");
            $adminUsers = $userRepository->findBy(["status" => "Administrateur"]);
            $emailService->sendMail($emailSubjectUser, [$user], ["confirmEmail" => true, "user" => $user]);
            $emailService->sendMail($emailSubjectAdmin, $adminUsers, ["newUser" => true, "user" => $user]);
            $notificationServices->newNotification($emailSubjectAdmin, $adminUsers, ["admin_user_show", $user->getId()]);
            $em->flush();
            $this->addFlash("success", "flash.user.addedSuccessfully");
            return $this->redirectToRoute('app_login');
        }
        return $this->render('security/signin.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @param User $user
     * @return RedirectResponse
     */
    #[Route("/confirm-account/{id}", name:"app_confirm_account")]
    public function confirmAccount(User $user): RedirectResponse
    {
        if ($user->getEmailCode() == null) {
            $this->addFlash("danger", "flash.user.account_already_validated");
          return $this->redirectToRoute("home");
        } else {
            $this->addFlash("success", "flash.user.account_validated");
            $user->setEmailCode(null);
            $user->setStatus($this->translator->trans(User::STATE_CLIENT, [], "NegasProjectTrans"));
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
            return $this->redirectToRoute("app_login");
        }
    }

    /**
     * @Route("/logout", name="app_logout")
     * @throws Exception
     */
    public function logout()
    {
        throw new Exception('This method can be blank - it will be intercepted by the logout key on your firewall');
    }
}
