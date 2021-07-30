<?php


namespace App\Datatables;


use App\Entity\News;
use Sg\DatatablesBundle\Datatable\AbstractDatatable;
use Sg\DatatablesBundle\Datatable\Column\ActionColumn;
use Sg\DatatablesBundle\Datatable\Column\Column;
use Sg\DatatablesBundle\Datatable\Column\DateTimeColumn;
use Sg\DatatablesBundle\Datatable\Column\ImageColumn;
use Sg\DatatablesBundle\Datatable\Style;

class NewsDatatable extends AbstractDatatable
{

    /**
     * @inheritDoc
     * @throws \Exception
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
            ->add("id", Column::class, [
                "visible" => false
            ])
            ->add('title', Column::class, [
                "title" => $this->translator->trans("news.subject", [], "NegasProjectTrans"),
                'searchable' => true,
                'orderable' => true,
            ])
            ->add("content", Column::class, [
                "title" => $this->translator->trans("news.content", [], "NegasProjectTrans"),
            ])
            ->add("product.filenameJpg", ImageColumn::class, [
                "title" => $this->translator->trans('product.label.jpg_file', [], 'NegasProjectTrans'),
                "imagine_filter" => "thumb",
                "relative_path" => "images/product",
                'searchable' => true,
                'orderable' => true,
                "width" => "30%"
            ])
            ->add("created_at", DateTimeColumn::class, [
                "title" =>$this->translator->trans('news.created_at', [], 'NegasProjectTrans'),
                'searchable' => true,
                'orderable' => true,
            ])
            ->add('enabled', Column::class, [
                'title' => $this->translator->trans('table.disabled', [], 'NegasProjectTrans'),
                'visible' => false,
            ])->add(null, ActionColumn::class, [
                    'title' => 'Actions',
                    'start_html' => '<div class="start_actions" style="text-align: center; display: flex; justify-content: center">',
                    "width" => "70px",
                    'end_html' => '</div>',
                    'actions' => [
                        [
                            'route' => 'admin_news_show',
                            'route_parameters' => [
                                'id' => 'id'
                            ],
                            'icon' => 'fas fa-eye fa-2x',
                            'attributes' => [
                                'rel' => 'tooltip',
                                'title' => $this->translator->trans('news.label.detail', [], 'NegasProjectTrans'),
                                'class' => 'btn btn-primary btn-sm m-2',
                                'role' => 'button'
                            ],
                        ],
                        [
                            'route' => 'admin_news_edit',
                            'route_parameters' => [
                                'id' => 'id'
                            ],
                            'icon' => 'fas fa-edit fa-2x',
                            'attributes' => [
                                'rel' => 'tooltip',
                                'title' => $this->translator->trans('news.label.edit', [], 'NegasProjectTrans'),
                                'class' => 'btn btn-warning btn-sm m-2',
                                'role' => 'button'
                            ],
                        ],
                        [
                            'route' => 'admin_news_enabled',
                            'route_parameters' => [
                                'id' => 'id'
                            ],
                            'icon' => 'fas fa-toggle-off fa-2x',
                            'attributes' => [
                                'rel' => 'tooltip',
                                'title' => $this->translator->trans('table.enabled', [], 'NegasProjectTrans'),
                                'class' => 'btn btn-danger btn-sm m-2',
                                'role' => 'button'
                            ],
                            'render_if' => function ($row) {
                                return !$row['enabled'];
                            }
                        ],
                        [
                            'route' => 'admin_news_enabled',
                            'route_parameters' => [
                                'id' => 'id'
                            ],
                            'icon' => 'fas fa-toggle-on fa-2x',
                            'attributes' => [
                                'rel' => 'tooltip',
                                'title' => $this->translator->trans('table.disabled', [], 'NegasProjectTrans'),
                                'class' => 'btn btn-success btn-sm m-2',
                                'role' => 'button'
                            ],
                            'render_if' => function ($row) {
                                return $row['enabled'];
                            },
                        ],
                    ]
                ]
            );;
    }

    /**
     * @inheritDoc
     */
    public function getEntity(): string
    {
        return News::class;
    }

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return "news_datatable";
    }
}
