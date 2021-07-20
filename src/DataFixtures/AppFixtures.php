<?php

namespace App\DataFixtures;

use App\Entity\User;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    /**
     * @var UserPasswordEncoderInterface
     */
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {
        $user = new User();
        $user
            ->setEmail("sl0ders@gmail.com")
            ->setPassword($this->encoder->encodePassword($user, '258790'))
            ->setRoles(["ROLE_ADMIN"])
            ->setName("Sommesous")
            ->setFirstName("Quentin")
            ->setUsername("sl0ders")
            ->setStatus("administrateur")
            ->setCreatedAt(new DateTime());
        $manager->persist($user);
        $manager->flush();
    }
}
