<?php


namespace App\Form;


use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class Public_UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', EmailType::class, [
                'attr' => [
                    "placeholder" => "Adresse email"
                ],
                'label' => "Modifier votre adresse email"
            ])
            ->add('firstname', TextType::class, [
                'attr' => [
                    'placeholder' => 'Votre prenom'
                ],
                'label' => "Modifier votre prenom"
            ])
            ->add('name', TextType::class, [
                'attr' => [
                    'placeholder' => 'Votre Nom'
                ],
                'label' => "Modifier votre Nom"
            ])
            ->add('username', TextType::class, [
                'attr' => [
                    'placeholder' => 'Votre pseudo'
                ],
                'label' => "Modifier votre pseudo"
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}