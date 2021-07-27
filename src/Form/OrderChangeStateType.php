<?php


namespace App\Form;


use App\Entity\Orders;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OrderChangeStateType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('Validation', ChoiceType::class, [
                'choices' => [
                    Orders::STATE_VALIDATE => Orders::STATE_VALIDATE,
                    Orders::STATE_COMPLETED => Orders::STATE_COMPLETED,
                    Orders::STATE_HONORED => Orders::STATE_HONORED,
                    Orders::STATE_ABORDED => Orders::STATE_ABORDED
                ],
                "attr" => ["class" => "select form-control mb-2 browser-default"],
            ])
            ->add("submit", SubmitType::class,  [
                "label" => "button.save",
                "attr" => ["class" => "btn btn-success"]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            "translation_domain" => "NegasProjectTrans",
            'data_class' => Orders::class,
        ]);
    }
}
