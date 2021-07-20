<?php

namespace App\Twig;

use App\Entity\OrderCartridge;
use Twig\Environment;
use Twig\Error\Error;

/**
 * Class ModelDocumentPrinter
 *
 * @package App\BB1\OrderCartridgeBundle\Utils
 * @author Terence <terence@numeric-wave.tech>
 */
class ModelDocumentPrinter
{
    /**
     * @var Environment
     */
    private $templating;

    /**
     * List of options => WkhtmlToPdf
     */
    private $options;

    /**
     * ModelDocumentPrinter constructor
     *
     * @param Environment $templating
     */
    public function __construct(Environment $templating)
    {
        $this->templating = $templating;

        $this->options = array(
            'margin-top' => 30, 'margin-bottom' => 12,
            'margin-left' => 0, 'margin-right' => 0,
        );
    }

    /**
     * Create a basic model with Knp options
     *
     * @param OrderCartridge $orderCartridge
     * @return array
     */
    public function createBasicOptionsFromOrderCartridge(OrderCartridge $orderCartridge): array
    {
        try {
            $header = $this->templating->render('Printing:headerPdf-invoice.html.twig', array(
                'orderCartridge' => $orderCartridge
            ));
        } catch (Error $twig_Error) {
            echo 'Twig_Error has been thrown - Header model document ' . $twig_Error->getMessage();
        }

        try {
            $footer = $this->templating->render('Printing:footerPdf-orderCartridge-simple.html.twig', array(
                'orderCartridge' => $orderCartridge,
            ));
        } catch (Error $twig_Error) {
            echo 'Twig_Error has been thrown - Footer model document ' . $twig_Error->getMessage();
        }

        $this->options['header-html'] = $header;
        $this->options['footer-html'] = $footer;

        return $this->options;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'bb1.utils.printer.model_orderCartridge';
    }
}
