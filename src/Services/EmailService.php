<?php

namespace App\Services;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;

/**
 * Class EmailService
 * @package App\Services
 */
class EmailService
{

    /**
     * @var MailerInterface
     */
    private MailerInterface $mailer;
    /**
     * @var UserRepository
     */
    private UserRepository $userRepository;

    public function __construct(MailerInterface $mailer, UserRepository $userRepository)
    {
        $this->mailer = $mailer;
        $this->userRepository = $userRepository;
    }

    /**
     * @param String $subject
     * @param array $receivers
     * @param array $context []
     */
    public function sendMail(string $subject, array $receivers, array $context = [])
    {
        // for each user of an company
        foreach ($receivers as $receiver) {
            //creation of an email
            $email = (new TemplatedEmail())
                ->from("sl0ders@gmail.com")
                ->to($receiver->getEmail())
                ->replyTo("sl0ders@gmail.com")
                ->subject($subject)
                ->htmlTemplate("Emails/alert_email.html.twig")
                ->context($context);
            try {
                $this->mailer->send($email);
            } catch (TransportExceptionInterface $e) {
                echo 'Exception reçue : ', $e->getMessage(), "\n";
            }
        }
    }
}
