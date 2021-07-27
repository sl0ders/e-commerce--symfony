<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class Admin_UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('Status', ChoiceType::class, [
                'choices' => [
                   User::STATE_VISITOR =>  User::STATE_VISITOR ,
                    User::STATE_CLIENT => User::STATE_CLIENT,
                    User::STATE_ADMINISTRATOR => User::STATE_ADMINISTRATOR,
                    User::STATE_BAN => User::STATE_BAN
                ]
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
