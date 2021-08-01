<?php


namespace App\Form;

use App\Entity\Package;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PackageType extends ApplicationType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add("quantity", IntegerType::class, [
                    "label" => "package.label.quantity",
                    "attr" => ['class' => "form-control mb-3"],
                ]
            )
            ->add("unity", TextType::class, [
                    "label" => 'package.label.unity',
                    "attr" => ['class' => "form-control mb-3"]
                ]
            )
            ->add("conditioning", TextType::class, [
                    "label" => 'package.label.conditioning',
                    "attr" => ['class' => "form-control mb-3"]
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            "translation_domain" => "NegasProjectTrans",
            'data_class' => Package::class,
            'attr' => ['class' => "d-flex align-items-center justify-around flex-column"]
        ]);
    }
}
