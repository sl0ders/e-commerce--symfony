<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

/**
 * Class PercentExtension
 *
 * @package Flit\CoreBundle\Twig
 * @author Térence <terence@numeric-wave.tech>
 */
class PercentExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return array(
            new TwigFilter('percent', array($this, 'percentFilter')),
        );
    }

    /**
     * Format percent number with params
     *
     * @param $number
     * @param int $decimals
     * @param string $decPoint
     * @param string $thousandsSep
     * @return float|int|string
     */
    public function percentFilter($number, $decimals = 2, $decPoint = ',', $thousandsSep = ' ')
    {
        $percent = $number * 100;

        $percent = number_format($percent, $decimals, $decPoint, $thousandsSep);
        $percent = $percent . ' %';

        return $percent;
    }

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return 'twig.percent';
    }
}
