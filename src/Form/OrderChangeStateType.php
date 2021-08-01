<?php


namespace App\Form;


use App\Entity\Orders;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Security;
use Symfony\Contracts\Translation\TranslatorInterface;

class OrderChangeStateType extends AbstractType
{
    private TranslatorInterface $translator;
    private Security $security;

    public function __construct(TranslatorInterface $translator,Security $security)
    {
        $this->translator = $translator;
        $this->security = $security;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if ($this->security->getUser()->getStatus() == "Administrateur") {
            $builder->add('validation', ChoiceType::class, [
                'choices' => [
                    Orders::STATE_VALIDATE => $this->translator->trans(Orders::STATE_IN_COURSE, [], 'NegasProjectTrans'),
                    Orders::STATE_COMPLETED => $this->translator->trans(Orders::STATE_COMPLETED, [], 'NegasProjectTrans'),
                    Orders::STATE_HONORED => $this->translator->trans(Orders::STATE_HONORED, [], 'NegasProjectTrans'),
                    Orders::STATE_ABORDED => $this->translator->trans(Orders::STATE_ABORDED, [], 'NegasProjectTrans')
                ],
                "attr" => ["class" => "select form-control mb-2 browser-default"],
            ]);
        } elseif ($this->security->getUser()->getStatus() == "Client") {
            $builder->add('validation', ChoiceType::class, [
                'choices' => [
                    $this->translator->trans("user.order.aborde", [], 'NegasProjectTrans') => $this->translator->trans(Orders::STATE_ABORDED, [], 'NegasProjectTrans')
                ],
                "attr" => ["class" => "select form-control mb-2 browser-default"],
            ]);
        }
            $builder->add("submit", SubmitType::class,  [
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
