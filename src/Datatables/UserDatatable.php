<?php


namespace App\Datatables;


use App\Entity\User;
use Sg\DatatablesBundle\Datatable\AbstractDatatable;
use Sg\DatatablesBundle\Datatable\Column\ActionColumn;
use Sg\DatatablesBundle\Datatable\Column\Column;
use Sg\DatatablesBundle\Datatable\Column\DateTimeColumn;
use Sg\DatatablesBundle\Datatable\Column\VirtualColumn;
use Sg\DatatablesBundle\Datatable\Style;

class UserDatatable extends AbstractDatatable
{

    /**
     * @return callable|Closure|null
     */
    public function getLineFormatter()
    {
        $formatter = function ($row) {

            $row['statusVirtual'] = $this->translator->trans($row['status'], [], 'NegasProjectTrans');
            return $row;
        };

        return $formatter;
    }

    /**
     * @inheritDoc
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
            ->add('email', Column::class, [
                'title' => $this->translator->trans('user.label.email', [], 'NegasProjectTrans'),
                'searchable' => true,
                'orderable' => true,
                "width" => "30%"
            ])
            ->add('status', Column::class, array(
                'title' => $this->translator->trans('user.label.state', [], 'NegasProjectTrans'),
                'visible' => false,
            ))
            ->add('statusVirtual', VirtualColumn::class, [
                'title' => $this->translator->trans('user.label.state', [], 'NegasProjectTrans'),
                'orderable' => true,
                'order_column' => 'status',
                "width" => "20%"
            ])
            ->add('roles', Column::class, [
                'title' => $this->translator->trans('user.label.roles', [], 'NegasProjectTrans'),
                'searchable' => true,
                'orderable' => true,
                "width" => "20%"
            ])
            ->add('created_at', DateTimeColumn::class, [
                'title' => $this->translator->trans('user.label.created_at', [], 'NegasProjectTrans'),
                'searchable' => true,
                'orderable' => true,
                "width" => "20%"
            ])->add('enabled', Column::class, [
                'title' => $this->translator->trans('table.disabled', [], 'NegasProjectTrans'),
                'visible' => false,
            ])->add(null, ActionColumn::class, [
                    'title' => 'Actions',
                    'start_html' => '<div class="start_actions" style="text-align: center; display: flex; justify-content: center">',
                    "width" => "70px",
                    'end_html' => '</div>',
                    'actions' => [
                        [
                            'route' => 'admin_user_enabled',
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
                            },
                        ],
                        [
                            'route' => 'admin_user_enabled',
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
                        ],[
                            'route' => 'admin_user_show',
                            'route_parameters' => [
                                'id' => 'id'
                            ],
                            'icon' => 'fas fa-eye fa-2x',
                            'attributes' => [
                                'rel' => 'tooltip',
                                'title' => $this->translator->trans('product.label.detail', [], 'NegasProjectTrans'),
                                'class' => 'btn btn-primary btn-sm m-2',
                                'role' => 'button'
                            ],
                        ],
                    ]
                ]
            );
    }

    /**
     * @inheritDoc
     */
    public function getEntity()
    {
        return User::class;
    }

    /**
     * @inheritDoc
     */
    public function getName()
    {
        return 'datatable_user';
    }
}
