<?php

namespace App\Form;

use App\Entity\Contact;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ContactType extends ApplicationType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstname', TextType::class, [
                "label" => "contact.firstName",
                "attr" => ["class" => "form-control"]
            ])
            ->add('email', TextType::class, [
                "label" => "contact.email",
                "attr" => ["class" => "form-control"]
            ])
            ->add('subject', TextType::class,  [
                "label" => "contact.subject",
                "attr" => ["class" => "form-control"]
            ])
            ->add('message', TextareaType::class, [
                "label" => "contact.message",
                "attr" => ["class" => "form-control"]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            "translation_domain" => "NegasProjectTrans",
            'data_class' => Contact::class,
        ]);
    }
}
