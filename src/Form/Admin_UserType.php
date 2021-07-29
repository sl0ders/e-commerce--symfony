<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

class Admin_UserType extends AbstractType
{
    private TranslatorInterface $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('Status', ChoiceType::class, [
                'choices' => [
                    User::STATE_VISITOR => $this->translator->trans(User::STATE_VISITOR, [], "NegasProjectTrans"),
                    User::STATE_CLIENT => $this->translator->trans(User::STATE_CLIENT, [], "NegasProjectTrans"),
                    User::STATE_ADMINISTRATOR => $this->translator->trans(User::STATE_ADMINISTRATOR, [], "NegasProjectTrans"),
                    User::STATE_BAN => $this->translator->trans(User::STATE_BAN, [], "NegasProjectTrans"),
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            "translation_domain" => "NegasProjectTrans",
            'data_class' => User::class,
        ]);
    }
}
