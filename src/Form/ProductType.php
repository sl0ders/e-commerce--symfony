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
            ->add('name', TextType::class, $this->getConfig('Nom du produit', 'Entrez le nom du produit'))
            ->add('quantity', IntegerType::class, $this->getConfig('Choisissez la quantitée entrée', 'Quantité'))
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                "attr" => ["class" => "form-control"],
            ])
            ->add('package', EntityType::class, [
                'label' => 'Choisissez le type de package',
                "class" => Package::class,
                "choice_label" => "packaging",
                "attr" => ["class" => "mdb-select md-form"]
            ])
            ->add('price', MoneyType::class, [
                "currency" => "EUR",
                'label' => 'Prix',
                "attr" => ["placeholder" => "Prix du package", "class" => "form-control", 'type' => 'number'],
            ])
            ->add('pictureFiles', FileType::class, [
                "label" => 'Image JPG',
                'required' => false,
                'multiple' => true,
                "attr" => ["class" => "file-path validate", "placeholder" => "Choisissez une image en format JPG"]
            ])
            ->add('pictureFilesPng', FileType::class, [
                "label" => 'Image PGN',
                'required' => false,
                'multiple' => true,
                "attr" => ["class" => "file-path validate", "placeholder" => "Choisissez une image en format PNG"]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
            "attr" => ["class" => "md-form"]
        ]);
    }
}
