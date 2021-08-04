<?php

namespace App\Datatables;

use App\Entity\Notification;
use App\Entity\Orders;
use App\Entity\Report;
use App\Entity\User;
use Closure;
use Exception;
use Sg\DatatablesBundle\Datatable\AbstractDatatable;
use Sg\DatatablesBundle\Datatable\Column\ActionColumn;
use Sg\DatatablesBundle\Datatable\Column\BooleanColumn;
use Sg\DatatablesBundle\Datatable\Column\Column;
use Sg\DatatablesBundle\Datatable\Column\DateTimeColumn;
use Sg\DatatablesBundle\Datatable\Column\VirtualColumn;
use Sg\DatatablesBundle\Datatable\Editable\CombodateEditable;
use Sg\DatatablesBundle\Datatable\Filter\TextFilter;
use Sg\DatatablesBundle\Datatable\Style;

/**
 * Class NotificationDatatable
 *
 * @package App\Datatables
 */
class ReportDatatable extends AbstractDatatable
{
    /**
     * @return callable|Closure|null
     */
    public function getLineFormatter(): callable|Closure|null
    {
        return function ($row) {
            $report = $this->em->getRepository(Report::class)->find($row["id"]);
            /** @var User $user */
            $user = $report->getUser();
            $row['userFullName'] = $user->getFullname();
            return $row;
        };
    }

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
            ->add("reportNumber", Column::class, [
                'title' => $this->translator->trans('report.label.reportCode', [], 'NegasProjectTrans'),
                'searchable' => true,
                'orderable' => true,
                'filter' => array(TextFilter::class, array()),
            ])
            ->add('userFullName', VirtualColumn::class, [
                "title" => $this->translator->trans('report.label.user', [], 'NegasProjectTrans')
            ])
            ->add("createdAt", DateTimeColumn::class, [
                'title' => $this->translator->trans('report.label.date', [], 'NegasProjectTrans'),
                "date_format" => 'DD-MM-YYYY H:m:s',
                'searchable' => true,
                'orderable' => true,
                "width" => "40px",
                'filter' => array(TextFilter::class, array()),
            ])
            ->add('status', Column::class,[
                "visible" => false,
                "title" => "actif"
            ])
            ->add(null, ActionColumn::class, array(
                'start_html' => '<div class="start_actions" style="width:60px; text-align: center; margin:auto">',
                'title' => $this->translator->trans('sg.datatables.actions.title'),
                'end_html' => '</div>',
                'actions' => [
                    [
                        'route' => "user_report_show",
                        'route_parameters' => array(
                            'id' => 'id'
                        ),
                        'icon' => 'fa fa-eye fa-2x',
                        'attributes' => [
                            'rel' => 'tooltip',
                            'title' => $this->translator->trans('report.link.show', [], 'NegasProjectTrans'),
                            'class' => 'btn btn-primary btn-sm m-2',
                            'role' => 'button'
                        ],
                    ], [
                        'route' => 'user_report_enabled',
                        'route_parameters' => ['id' => 'id'],
                        'icon' => 'fa fa-toggle-off fa-2x',
                        'attributes' => [
                            'rel' => 'tooltip',
                            'class' => 'btn btn-danger btn-sm m-2',
                            'role' => 'button',
                            'title' => $this->translator->trans('report.link.archived', [], 'NegasProjectTrans'),
                        ],
                        'render_if' => function ($row): bool {
                            return !$row['status'];
                        },
                    ], [
                        'route' => 'user_report_enabled',
                        'route_parameters' => ['id' => 'id'],
                        'icon' => 'fa fa-toggle-on fa-2x',
                        'attributes' => [
                            'rel' => 'tooltip',
                            'class' => 'btn btn-primary btn-sm m-2',
                            'role' => 'button',
                            'title' => $this->translator->trans('report.link.desarchived', [], 'NegasProjectTrans'),
                        ],
                        'render_if' => function ($row): bool {
                            return $row['status'];
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
        return Report::class;
    }

    public function getName(): string
    {
        return "report_datatable";
    }
}
