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
                'label' => "user.label.email",
                "attr" => ["class" => "form-control"]
            ])
            ->add('password', PasswordType::class, [
                'label' => "user.label.password",
                "attr" => ["class" => "form-control"]
            ])
            ->add('confirm_password', PasswordType::class, [
                'label' => "user.label.rePassword",
                "attr" => ["class" => "form-control"]
            ])
            ->add('firstname', TextType::class, [
                'label' => "user.label.firstname",
                "attr" => ["class" => "form-control"]
            ])
            ->add('name', TextType::class, [
                'label' => "user.label.lastname",
                "attr" => ["class" => "form-control"]
            ])
            ->add('username', TextType::class, [
                'label' => "user.label.username",
                "attr" => ["class" => "form-control"],
                "required" => false
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            "translation_domain" => "NegasProjectTrans",
            'data_class' => User::class,
        ]);
    }
}
