<?php

namespace App\Form;

use App\Entity\Cartridge;
use App\Entity\OrderCartridge;
use App\Entity\Orders;
use App\Entity\Printer;
use App\Entity\Report;
use App\Repository\CartridgeRepository;
use App\Repository\OrderCartridgeRepository;
use App\Repository\OrdersRepository;
use App\Repository\PrinterRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Security;
use Symfony\Contracts\Translation\TranslatorInterface;

class ReportType extends AbstractType
{
    private Security $security;
    private TranslatorInterface $translator;

    public function __construct(Security $security, TranslatorInterface $translator)
    {
        $this->security = $security;
        $this->translator = $translator;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add("subject", TextType::class, [
                "label" => 'report.label.subject',
                'required' => true,
                "attr" => [
                    'class' => 'form-control required'
                ]
            ])
        ->add("submit", SubmitType::class, [
            "label" => "button.save",
            "attr" => ["class" => "btn btn-success"]
        ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'company' => false,
            "translation_domain" => "NegasProjectTrans",
            'data_class' => Report::class,
        ]);
    }
}
