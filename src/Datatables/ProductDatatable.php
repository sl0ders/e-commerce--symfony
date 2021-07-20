<?php

namespace App\Datatables;

use App\Entity\Product;
use Sg\DatatablesBundle\Datatable\AbstractDatatable;
use Sg\DatatablesBundle\Datatable\Column\ActionColumn;
use Sg\DatatablesBundle\Datatable\Column\Column;
use Sg\DatatablesBundle\Datatable\Column\DateTimeColumn;
use Sg\DatatablesBundle\Datatable\Column\ImageColumn;
use Sg\DatatablesBundle\Datatable\Style;

class ProductDatatable extends AbstractDatatable
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
            ->add('id', Column::class, [
                'title' => 'Id',
                'searchable' => true,
                'orderable' => true,
                "width" => "50px"
            ])
            ->add("name", Column::class, [
                "title" => "Nom du produit",
                "searchable" => true,
                "orderable" => true,
                "width" => "110px"
            ])
            ->add("description", Column::class, [
                "title" => "Description",
                "searchable" => true,
                "orderable" => true,
                "width" => "300px"
            ])
            ->add("price", Column::class, [
                "title" => "Prix",
                "searchable" => true,
                "orderable" => true,
                "width" => "50px"
            ])
            ->add("stock.quantity", Column::class, [
                "title" => "Quantité",
                "searchable" => true,
                "orderable" => true,
                "width" => "50px"
            ])
            ->add("updated_at", DateTimeColumn::class, [
                "title" => "Date de creation",
                "searchable" => true,
                "orderable" => true,
                "width" => "150px"
            ])
            ->add("filenameJpg", ImageColumn::class, [
                "title" => "Image jpg",
                "imagine_filter" => "thumb",
                "relative_path" => "images/product",
                "holder_width" => "50",
                "holder_height" => "50",
                "width" => "100px"
            ])
            ->add("filenamePng", ImageColumn::class, [
                "title" => "Image png",
                "imagine_filter" => "thumb",
                "relative_path" => "images/product",
                "holder_width" => "50",
                "holder_height" => "50",
                "width" => "100px"
            ])
            ->add("enabled", Column::class, [
                'title' => "désactiver",
                'visible' => false,
            ])
            ->add(null, ActionColumn::class, [
                    'title' => 'Actions',
                    'start_html' => '<div class="start_actions">',
                    "width" => "110px",
                    'end_html' => '</div>',
                    'actions' => [
                        [
                            'route' => 'admin_product_enabled',
                            'route_parameters' => [
                                'id' => 'id'
                            ],
                            'icon' => 'fa fa-toggle-off fa-1x',
                            'attributes' => [
                                'rel' => 'tooltip',
                                'title' => "enabled",
                                'class' => 'btn btn-danger m-2',
                                'role' => 'button'
                            ],
                            'render_if' => function ($row) {
                                return !$row['enabled'];
                            },
                        ],
                        [
                            'route' => 'admin_product_enabled',
                            'route_parameters' => [
                                'id' => 'id'
                            ],
                            'icon' => 'fa fa-toggle-on fa-1x',
                            'attributes' => [
                                'rel' => 'tooltip',
                                'title' => "disabled",
                                'class' => 'btn btn-success m-2 btn-sm',
                                'role' => 'button'
                            ],
                            'render_if' => function ($row) {
                                return $row['enabled'];
                            },
                        ],
                        [
                            'route' => 'admin_product_show',
                            'label' => null,
                            'route_parameters' => [
                                'id' => 'id',
                                '_format' => 'html',
                                '_locale' => 'fr'
                            ],
                            'icon' => 'fa fa-eye fa-1x',
                            'attributes' => [
                                'rel' => 'tooltip',
                                'title' => "Detail du produit",
                                'role' => 'button',
                                'class' => "btn btn-primary btn-sm m-2"
                            ],
                            'start_html' => '<div class="start_show_action">',
                            'end_html' => '</div>',
                        ], [
                            'route' => 'admin_product_edit',
                            'label' => null,
                            'route_parameters' => [
                                'id' => 'id',
                                '_format' => 'html',
                                '_locale' => 'fr'
                            ],
                            'icon' => 'fa fa-edit fa-1x',
                            'attributes' => [
                                'rel' => 'tooltip',
                                'title' => "Detail du produit",
                                'role' => 'button',
                                'class' => "btn btn-warning m-2 btn-sm"
                            ],
                            'start_html' => '<div class="start_show_action">',
                            'end_html' => '</div>',
                        ]
                    ]]
            );
    }

    public function getEntity(): string
    {
        return Product::class;
    }

    public function getName()
    {
        return 'datatable_product';
    }
}
