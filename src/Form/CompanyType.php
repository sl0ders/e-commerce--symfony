<?php

namespace App\Form;

use App\Entity\Company;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CompanyType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                "label" => "company.name",
                "attr"=> ["class" => "form-control"]
            ])
            ->add('address', TextType::class, [
                "label" => "company.address",
                "attr"=> ["class" => "form-control"]
            ])
            ->add('email', EmailType::class, [
                "label" => "company.email",
                "attr"=> ["class" => "form-control"]
            ])
            ->add('createdAt', DateTimeType::class, [
                "label" => "company.createdAt",
                "attr"=> ["class" => "form-control"],
                'widget' => 'single_text',
            ])
            ->add("submit", SubmitType::class, [
                "label" => "button.save",
                "attr" => ["class" => "btn btn-success"]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            "translation_domain" => "NegasProjectTrans",
            'data_class' => Company::class,
        ]);
    }
}
