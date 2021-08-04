<?php

namespace App\DataFixtures;

use App\Entity\Company;
use App\Entity\User;
use DateTime;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    /**
     * @var UserPasswordEncoderInterface
     */
    private UserPasswordEncoderInterface $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $em)
    {
        $company = new Company();
        $manager = new User();
        $manager
            ->setEmail("sl0ders@gmail.com")
            ->setPassword($this->encoder->encodePassword($manager, '258790'))
            ->setRoles(["ROLE_ADMIN"])
            ->setName("Sommesous")
            ->setFirstName("Quentin")
            ->setUsername("sl0ders")
            ->setStatus("Administrateur")
            ->setEnabled("true")
            ->setCreatedAt(new DateTimeImmutable());
        $em->persist($manager);
        $company
            ->setName("Teraneo")
            ->setCode(strtoupper(substr($company->getName(), -3)))
            ->setAddress("Rue Henri Marchal, 66510 Saint-Hippolyte")
            ->addManager($manager)
            ->setEmail("sl0ders@gmail.com")
            ->setCreatedAt(new DateTimeImmutable());
        $em->persist($company);
        $em->flush();
    }
}
