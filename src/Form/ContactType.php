<?php

namespace App\Form;

use App\Entity\Contact;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ContactType extends ApplicationType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username', TextType::class, $this->getConfig("Votre pseudo", "Entrez votre pseudo ..."))
            ->add('firstname', TextType::class, $this->getConfig("Votre prénom", "Entrez votre prénom ..."))
            ->add('email', TextType::class, $this->getConfig("Votre adresse email", "Entrez votre adresse email ..."))
            ->add('subject', TextType::class, $this->getConfig("Sujet du message", "Entrez le sujet de votre message ..."))
            ->add('message', TextareaType::class, ['label' => "Entrez votre message",
                'attr' => [
                    'placeholder' => "Entrez votre message",
                    'is' => 'textarea-autogrow',
                ]]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Contact::class,
        ]);
    }
}
