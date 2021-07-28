<?php

namespace App\Datatables;

use App\Entity\Notification;
use Exception;
use Sg\DatatablesBundle\Datatable\AbstractDatatable;
use Sg\DatatablesBundle\Datatable\Column\ActionColumn;
use Sg\DatatablesBundle\Datatable\Column\Column;
use Sg\DatatablesBundle\Datatable\Column\DateTimeColumn;
use Sg\DatatablesBundle\Datatable\Filter\TextFilter;
use Sg\DatatablesBundle\Datatable\Style;

/**
 * Class NotificationDatatable
 *
 * @package App\Datatables
 */
class NotificationDatatable extends AbstractDatatable
{
    /**
     * {@inheritdoc}
     * @throws Exception
     */
    public function buildDatatable(array $options = array())
    {
        $this->ajax->set(array(
            // send some extra example data
            'data' => array('data1' => 1, 'data2' => 2),
            // cache for 10 pages
            'pipeline' => 10
        ));

        $this->language->set(array(
            'cdn_language_by_locale' => true
        ));

        $this->options->set(array(
            'classes' => Style::BOOTSTRAP_4_STYLE,
            'stripe_classes' => ['strip1', 'strip2', 'strip3'],
            'individual_filtering' => true,
            'individual_filtering_position' => 'head',
            'order' => array(array(0, 'asc')),
            'order_cells_top' => true,
            'paging_type' => Style::FULL_NUMBERS_PAGINATION,
            "page_length" => 40
        ));
        $this->columnBuilder
            ->add("createdAt", DateTimeColumn::class, [
                'title' => $this->translator->trans('notification.label.date', [], 'NegasProjectTrans'),
                "date_format" => 'DD-MM-YYYY H:m:s',
                'searchable' => true,
                'orderable' => true,
                "width" => "30%",
                'filter' => array(TextFilter::class, array()),
            ])
            ->add("message", Column::class, [
                'title' => $this->translator->trans('notification.label.message', [], 'NegasProjectTrans'),
                'searchable' => true,
                'orderable' => true,
                "width" => "50%",
                'filter' => array(TextFilter::class, array())
            ])
            ->add('isEnabled', Column::class,[
                "visible" => false,
                "title" => "actif"
            ])
            ->add(null, ActionColumn::class, array(
                'start_html' => '<div class="start_actions" style="text-align: center; display: flex; justify-content: space-around">',
                'title' => $this->translator->trans('sg.datatables.actions.title'),
                "width" => "10%",
                'end_html' => '</div>',
                'actions' => [
                    [
                        'route' => "user_notification_show",
                        'route_parameters' => array(
                            'id' => 'id'
                        ),
                        'icon' => 'fa fa-eye fa-2x',
                        'attributes' => [
                            'rel' => 'tooltip',
                            'title' => $this->translator->trans('notification.link.show', [], 'NegasProjectTrans'),
                            'class' => 'btn btn-primary btn-sm m-2',
                            'role' => 'button'
                        ],
                    ], [
                        'route' => 'user_notification_enabled',
                        'route_parameters' => ['id' => 'id'],
                        'icon' => 'fa fa-toggle-off fa-2x',
                        'attributes' => [
                            'rel' => 'tooltip',
                            'class' => 'btn btn-danger btn-sm m-2',
                            'role' => 'button',
                            'title' => $this->translator->trans('notification.link.archived', [], 'NegasProjectTrans'),
                        ],
                        'render_if' => function ($row): bool {
                            return !$row['isEnabled'];
                        },
                    ], [
                        'route' => 'user_notification_enabled',
                        'route_parameters' => ['id' => 'id'],
                        'icon' => 'fa fa-toggle-on fa-2x',
                        'attributes' => [
                            'rel' => 'tooltip',
                            'class' => 'btn btn-success btn-sm m-2',
                            'role' => 'button',
                            'title' => $this->translator->trans('notification.link.desarchived', [], 'NegasProjectTrans'),
                        ],
                        'render_if' => function ($row): bool {
                            return $row['isEnabled'];
                        },
                    ],
                ],
            ));
    }


    /**
     * {@inheritdoc}
     */
    public function getEntity(): string
    {
        return Notification::class;
    }

    public function getName(): string
    {
        return "notification_datatable";
    }
}
