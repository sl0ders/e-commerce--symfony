<?php

namespace App\Form;

use App\Entity\News;
use App\Entity\Product;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;

class NewsType extends ApplicationType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('Title', TextType::class, [
                "label" => "news.label.title",
                "attr" => ["class" => "form-control"]
            ])
            ->add('content', TextareaType::class,[
                "label" => "news.label.content",
                "attr" => ["class" => "form-control"]
            ])
            ->add('product', EntityType::class, [
                'attr' => [
                    'class' => 'browser-default custom-select form-control'
                ],
                'class' => Product::class,
                'label' => 'news.label.product'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            "translation_domain" => "NegasProjectTrans",
            'data_class' => News::class,
        ]);
    }
}
