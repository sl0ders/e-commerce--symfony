<?php

namespace App\Form;


use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', EmailType::class, [
                'label' => "Entrez votre adresse email",
                "attr" => ["class" => "form-control"]
            ])
            ->add('password', PasswordType::class, [
                'label' => "Mot de passe",
                "attr" => ["class" => "form-control"]
            ])
            ->add('confirm_password', PasswordType::class, [
                'label' => "Confirmation de votre mot de passe",
                "attr" => ["class" => "form-control"]
            ])
            ->add('firstname', TextType::class, [
                'label' => "Entrez votre prenom",
                "attr" => ["class" => "form-control"]
            ])
            ->add('name', TextType::class, [
                'label' => "Entrez votre Nom",
                "attr" => ["class" => "form-control"]
            ])
            ->add('username', TextType::class, [
                'label' => "Entrez votre pseudo",
                "attr" => ["class" => "form-control"]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
