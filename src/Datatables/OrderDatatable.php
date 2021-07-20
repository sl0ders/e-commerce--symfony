<?php


namespace App\Datatables;


use App\Entity\Orders;
use Exception;
use Sg\DatatablesBundle\Datatable\AbstractDatatable;
use Sg\DatatablesBundle\Datatable\Column\ActionColumn;
use Sg\DatatablesBundle\Datatable\Column\Column;
use Sg\DatatablesBundle\Datatable\Column\DateTimeColumn;
use Sg\DatatablesBundle\Datatable\Style;

class OrderDatatable extends AbstractDatatable
{
    /**
     * @throws Exception
     */
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
            ->add('id', Column::class, [
                'title' => 'id',
                "visible" => false
            ])
            ->add("user.name", Column::class, [
                'title' => "Commande effectué par...",
                'searchable' => true,
                'orderable' => true
            ])
            ->add('nCmd', Column::class, [
                'title' => 'Numero de commande',
                'searchable' => true,
                'orderable' => true,
            ])
            ->add('created_at', DateTimeColumn::class, [
                'title' => 'Date de création',
                'searchable' => true,
                'orderable' => true,
            ])
            ->add('quantity', Column::class, [
                'title' => 'Quantité',
                'searchable' => true,
                'orderable' => true,
            ])
            ->add('total', Column::class, [
                'title' => 'Total',
                'searchable' => true,
                'orderable' => true,
            ])
            ->add('validation', Column::class, [
                'title' => 'État de la commande',
                "class_name" => "validation",
                'searchable' => true,
                'orderable' => true,
            ])
            ->add(null, ActionColumn::class, [
                    'title' => 'Actions',
                    'start_html' => '<div class="start_actions">',
                    "width" => "110px",
                    'end_html' => '</div>',
                    'actions' => [
                        [
                            'route' => 'admin_orders_show',
                            'label' => null,
                            'route_parameters' => [
                                'id' => 'id',
                                '_format' => 'html',
                                '_locale' => 'fr'
                            ],
                            'icon' => 'fas fa-file-pdf fa-2x',
                            'attributes' => [
                                'rel' => 'tooltip',
                                'title' => "Detail de la commande",
                                'role' => 'button',
                                'class' => "btn btn-primary btn-sm m-2"
                            ],
                            'start_html' => '<div class="start_show_action">',
                            'end_html' => '</div>',
                        ]
                    ]
                ]
            );
    }

    public function getEntity(): string
    {
        return Orders::class;
    }

    public function getName(): string
    {
        return "datatable_order";
    }
}
