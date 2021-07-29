<?php


namespace App\Form;


use App\Entity\Orders;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

class OrderChangeStateType extends AbstractType
{
    private TranslatorInterface $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('Validation', ChoiceType::class, [
                'choices' => [
                    Orders::STATE_VALIDATE => $this->translator->trans(Orders::STATE_IN_COURSE, [], 'NegasProjectTrans'),
                    Orders::STATE_COMPLETED => $this->translator->trans(Orders::STATE_COMPLETED, [], 'NegasProjectTrans'),
                    Orders::STATE_HONORED => $this->translator->trans(Orders::STATE_HONORED, [], 'NegasProjectTrans'),
                    Orders::STATE_ABORDED => $this->translator->trans(Orders::STATE_ABORDED, [], 'NegasProjectTrans')
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
