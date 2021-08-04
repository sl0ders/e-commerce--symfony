<?php

namespace App\Form;

use App\Entity\MessageReport;
use App\Entity\ReportMessage;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ReportMessageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('message', TextareaType::class, [
                "attr" => ["class" => "m-auto rounded border-white", "cols" => 50, "rows" => 5, "placeholder" => "report_message.label.addMessage"],
                "label" => false
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'translation_domain' => "NegasProjectTrans",
            'data_class' => ReportMessage::class,
        ]);
    }
}
