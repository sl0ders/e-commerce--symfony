<?php

namespace App\Form;

use App\Entity\Package;
use App\Entity\Product;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductType extends ApplicationType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                "label" => 'product.label.name',
                "attr" => ["class" => "form-control"],
            ])
            ->add('quantity', IntegerType::class,[
                "label" => 'product.label.quantity',
                "attr" => ["class" => "form-control"],
                "mapped" => false

            ])
            ->add('description', TextareaType::class, [
                'label' => 'product.label.description',
                "attr" => ["class" => "form-control"],
            ])
            ->add('package', EntityType::class, [
                'label' => 'product.label.package',
                "class" => Package::class,
                "choice_label" => "packaging",
                "attr" => ["class" => "mdb-select md-form"]
            ])
            ->add('price', MoneyType::class, [
                "currency" => "EUR",
                'label' => 'product.label.price',
                "attr" => ["class" => "form-control", 'type' => 'number'],
            ])
            ->add('pictureFiles', FileType::class, [
                "label" => "product.label.jpg_file",
                'required' => false,
                'multiple' => true,
                "attr" => ["class" => "file-path validate"]
            ])
            ->add('pictureFilesPng', FileType::class, [
                "label" => 'product.label.png_file',
                'required' => false,
                'multiple' => true,
                "attr" => ["class" => "file-path validate"]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            "translation_domain" => "NegasProjectTrans",
            'data_class' => Product::class,
            "attr" => ["class" => "md-form"]
        ]);
    }
}
