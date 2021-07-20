<?php


namespace App\Form;

use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class PackageType extends ApplicationType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add("quantity", IntegerType::class, $this->getConfig("Quantité", "Quantité"))
            ->add("unity", TextType::class, $this->getConfig('Unité (g, kg, l...)', "Unité"));
    }
}
