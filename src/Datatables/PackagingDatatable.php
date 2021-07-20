<?php


namespace App\Datatables;


use App\Entity\Package;
use Sg\DatatablesBundle\Datatable\AbstractDatatable;
use Sg\DatatablesBundle\Datatable\Column\Column;
use Sg\DatatablesBundle\Datatable\Style;

class PackagingDatatable extends AbstractDatatable
{

    public function buildDatatable(array $options = [])
    {
        $this->language->set(array(
            'cdn_language_by_locale' => true,
        ));
        $this->ajax->set([
            // send some extra example data
            'data' => ['data1' => 1, 'data2' => 2],
            // cache for 10 pages
            'pipeline' => 10
        ]);
        $this->options->set([
            'classes' => Style::BOOTSTRAP_4_STYLE,
            'stripe_classes' => ['strip1', 'strip2', 'strip3'],
            'individual_filtering' => true,
            'individual_filtering_position' => 'head',
            'order' => [
                [
                    0, 'asc'
                ]
            ],
            'order_cells_top' => true,
            'search_in_non_visible_columns' => true,
        ]);

        $this->columnBuilder
            ->add('quantity', Column::class, [
                'title' => 'Quantité',
                'searchable' => true,
                'orderable' => true,
            ])->add('unity', Column::class, [
                'title' => 'Unité',
                'searchable' => true,
                'orderable' => true,
            ]);
    }

    public function getEntity()
    {
        return Package::class;
    }

    public function getName()
    {
        return "datatable_packaging";
    }
}
