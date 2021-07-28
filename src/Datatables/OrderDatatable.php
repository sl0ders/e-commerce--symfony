<?php


namespace App\Datatables;


use App\Entity\Orders;
use App\Entity\User;
use Closure;
use Exception;
use NumberFormatter;
use Sg\DatatablesBundle\Datatable\AbstractDatatable;
use Sg\DatatablesBundle\Datatable\Column\ActionColumn;
use Sg\DatatablesBundle\Datatable\Column\Column;
use Sg\DatatablesBundle\Datatable\Column\DateTimeColumn;
use Sg\DatatablesBundle\Datatable\Column\NumberColumn;
use Sg\DatatablesBundle\Datatable\Column\VirtualColumn;
use Sg\DatatablesBundle\Datatable\Style;

class OrderDatatable extends AbstractDatatable
{

    public function getLineFormatter(): Closure
    {
        return function ($row) {
            $order = $this->em->getRepository(Orders::class)->find($row["id"]);
            /** @var User $user */
            $user = $order->getUser();
            $row['userFullName'] = $user->getFullname();

            switch ($order->getValidation()) {
                case $order::STATE_IN_COURSE :
                    $row['state'] = $this->translator->trans($order::STATE_IN_COURSE, [], 'NegasProjectTrans');
                    break;
                case $order::STATE_VALIDATE :
                    $row['state'] = $this->translator->trans($order::STATE_VALIDATE, [], 'NegasProjectTrans');
                    break;
                case $order::STATE_COMPLETED :
                    $row['state'] = $this->translator->trans($order::STATE_COMPLETED, [], 'NegasProjectTrans');
                    break;
                case $order::STATE_HONORED :
                    $row['state'] = $this->translator->trans($order::STATE_HONORED, [], 'NegasProjectTrans');
                    break;
            }
            return $row;
        };
    }


    /**
     * @throws Exception
     */
    public function buildDatatable(array $options = [])
    {
        $formatter = new NumberFormatter("fr_FR", NumberFormatter::CURRENCY);

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
            ->add('nCmd', Column::class, [
                'title' => $this->translator->trans('orders.label.ncmd', [], 'NegasProjectTrans'),
                'searchable' => true,
                'orderable' => true,
            ])
            ->add("userFullName", VirtualColumn::class, array(
                'title' => $this->translator->trans('user.label.fullname', [], 'NegasProjectTrans'),
                'searchable' => true,
                'search_column' => 'isDefault',
                'order_column' => 'created_at',
                'orderable' => true,
            ))
            ->add('created_at', DateTimeColumn::class, [
                'title' => $this->translator->trans('orders.label.created_at', [], 'NegasProjectTrans'),
                'searchable' => true,
                'orderable' => true,
            ])
            ->add('total', NumberColumn::class, [
                'title' => $this->translator->trans('orders.label.total', [], 'NegasProjectTrans'),
                'searchable' => true,
                'formatter' => $formatter,
                'use_format_currency' => true, // needed for \NumberFormatter::CURRENCY
                'currency' => 'EUR',
                'orderable' => true,
            ])
            ->add('validation', Column::class, [
                'title' => $this->translator->trans('orders.label.state', [], 'NegasProjectTrans'),
                "class_name" => "validation",
                'searchable' => true,
                'orderable' => true,
            ])
            ->add(null, ActionColumn::class, [
                    'title' => 'Actions',
                    'start_html' => '<div class="start_actions" style="width:150px; text-align: center; display: flex; justify-content: space-between">',
                    "width" => "200px",
                    'end_html' => '</div>',
                    'actions' => [
                        [
                            'route' => 'admin_orders_show_pdf',
                            'label' => null,
                            'route_parameters' => [
                                'id' => 'id',
                                '_format' => 'html',
                                '_locale' => 'fr'
                            ],
                            'icon' => 'fas fa-file-pdf fa-2x',
                            'attributes' => [
                                'rel' => 'tooltip',
                                'title' => $this->translator->trans('orders.hover.detail-pdf', [], 'NegasProjectTrans'),
                                'role' => 'button',
                                'class' => "btn btn-danger btn-sm m-2"
                            ],
                            'start_html' => '<div class="start_show_action">',
                            'end_html' => '</div>',
                        ],[
                            'route' => 'admin_orders_show',
                            'label' => null,
                            'route_parameters' => [
                                'id' => 'id',
                                '_format' => 'html',
                                '_locale' => 'fr'
                            ],
                            'icon' => 'fas fa-eye fa-2x',
                            'attributes' => [
                                'rel' => 'tooltip',
                                'title' => $this->translator->trans('orders.hover.detail', [], 'NegasProjectTrans'),
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
