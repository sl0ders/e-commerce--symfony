<?php


namespace App\Services;


use App\Entity\Notification;
use App\Repository\ConsumableRepository;
use App\Repository\NotificationRepository;
use App\Repository\UserRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class NotificationServices
{
    private $notificationRepository, $em;
    private TokenStorageInterface $tokenStorage;
    private UserRepository $userRepository;

    public function __construct(
        NotificationRepository $notificationRepository,
        UserRepository $userRepository,
        TokenStorageInterface $tokenStorage,
        EntityManagerInterface $em
    )
    {
        $this->notificationRepository = $notificationRepository;
        $this->userRepository = $userRepository;
        $this->tokenStorage = $tokenStorage;
        $this->em = $em;
    }

    /*We determine the message to be notified, the user who sends and the user who will receive the notification, the fourth
     parameter is optional, it determines the path to which we will be directed by clicking on this notification*/
    /**
     * @throws Exception
     */
    public function newNotification($message, $receiver, $path = [])
    {
        $date = new DateTime();
        // This date is the time limit before filing the notification
        $dateM = $date->modify("+6 month");
        $datesync = new DateTime();
        $datesync = $datesync->format("d-m-Y H:i:s");
        $dateFinal = new DateTime($datesync);

        // we check if this notification exists, and if it does not exist we create a new one
        $notification = $this->notificationRepository->findOneBy(['message' => $message]);
        if (!isset($notification)) {
            $notification = new Notification();
            $notification->setIsEnabled(true)
                ->setCreatedAt($dateFinal)
                ->setMessage($message)
                ->setExpirationDate($dateM);
            if ($path) {
                //The first parameter of option is the path name and the second is the id
                $notification->setPath($path[0]);
                $notification->setIdPath($path[1]);
            }
            $this->em->persist($notification);
        }
        return true;
    }
}
