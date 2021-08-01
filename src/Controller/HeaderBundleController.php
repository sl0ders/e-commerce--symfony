<?php


namespace App\Controller;


use App\Entity\Notification;
use App\Repository\NotificationRepository;
use App\Repository\UserRepository;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * this class manage all system of header
 * Class HeaderBundleController
 * @package App\Controller
 */
class HeaderBundleController extends AbstractController
{

    /**
     * @var TranslatorInterface
     */
    private TranslatorInterface $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }
    /**
     * this function Manage the notification system of header
     * @param NotificationRepository $notificationRepository
     * @return Response
     * @throws Exception
     */
    public function getNotification(NotificationRepository $notificationRepository): Response
    {
        $notificationDescSorting = [];
        if ($this->getUser()) {// retrieve the user connected for reference in condition
            if ($this->container->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
                $notificationDescSorting = $notificationRepository->findBy([], ["createdAt" => "DESC"]);
            } else {
                $notifications = $this->getUser()->getNotifications();
                $iterator = $notifications->getIterator();
                $iterator->uasort(function ($a, $b) {
                    return ($a->getCreatedAt() > $b->getCreatedAt()) ? +1 : 1;
                });
                $notificationIterator = iterator_to_array($iterator);
                /** @var Notification $notification */
                foreach ($notificationIterator as $notification) {
                    if ($notification->getIsEnabled() === true) {
                        array_push($notificationDescSorting, $notification);
                    }
                }
            }
        }
        return $this->render("Layout/_header.html.twig", [
            "notifications" => $notificationDescSorting
        ]);
    }
}
