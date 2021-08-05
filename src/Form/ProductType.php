<?php

namespace App\Form;

use App\Entity\Package;
use App\Entity\Product;
use App\Repository\PackageRepository;
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
    private PackageRepository $packageRepository;

    public function __construct(PackageRepository $packageRepository)
    {
        $this->packageRepository = $packageRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                "label" => 'product.label.name',
                "attr" => ["class" => "form-control", "required" => "required"]
            ])
            ->add('quantity', IntegerType::class, [
                "label" => 'stock.label.quantity',
                "attr" => ["class" => "form-control", "required" => "required"],
                "mapped" => false
            ])
            ->add("packageValue", IntegerType::class, [
                "label" => "package.label.quantity",
                "attr" => ["class" => "form-control"],
                "mapped" => false,
                "required" => false
            ])
            ->add("unity", TextType::class, [
                "label" => "package.label.unity",
                "attr" => ["class" => "form-control"],
                "mapped" => false,
                "required" => false
            ])
            ->add("conditioning", TextType::class, [
                "label" => "stock.label.conditioning",
                "attr" => ["class" => "form-control"],
                "mapped" => false,
                "required" => false
            ])
            ->add('description', TextareaType::class, [
                'label' => 'product.label.description',
                "attr" => ["class" => "form-control"]
            ])
            ->add('package', EntityType::class, [
                'label' => 'product.label.package',
                "class" => Package::class,
                "choice_label" => "packaging",
                "attr" => ["class" => "mdb-select md-form"],
                "required" => false
            ])
            ->add('price', MoneyType::class, [
                "currency" => "EUR",
                'label' => 'product.label.price',
                "attr" => ["class" => "form-control", 'type' => 'number']
            ])
            ->add('pictureFiles', FileType::class, [
                "label" => "product.label.jpg_file",
                'multiple' => true,
                "attr" => ["class" => "file-path validate"]
            ])
            ->add('pictureFilesPng', FileType::class, [
                "label" => 'product.label.png_file',
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
