<?php


namespace App\Datatables;


use App\Entity\Package;
use Sg\DatatablesBundle\Datatable\AbstractDatatable;
use Sg\DatatablesBundle\Datatable\Column\ActionColumn;
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
                'title' => $this->translator->trans('package.label.quantity', [], 'NegasProjectTrans'),
                'searchable' => true,
                'orderable' => true,
                "width" => "40%"
            ])->add('unity', Column::class, [
                'title' => $this->translator->trans('package.label.unity', [], 'NegasProjectTrans'),
                'searchable' => true,
                'orderable' => true,
                "width" => "40%"
            ])->add('conditioning', Column::class, [
                'title' => $this->translator->trans('package.label.conditioning', [], 'NegasProjectTrans'),
                'searchable' => true,
                'orderable' => true,
                "width" => "40%"
            ])
            ->add(null, ActionColumn::class, [
                    "title" => $this->translator->trans('package.label.action', [], 'NegasProjectTrans'),
                    'start_html' => '<div style="text-align: center; display: flex; justify-content: center">',
                "width" => "20%",
                    'end_html' => '</div>',
                    'actions' => [
                        [
                            'confirm' => true,
                            'confirm_message' => $this->translator->trans('return.deleteQuestion', [], 'NegasProjectTrans'),
                            'start_html' => '<div class="start_delete_action">',
                            'end_html' => '</div>',
                            'route' => 'admin_package_delete',
                            'route_parameters' => [
                                'id' => 'id'
                            ],
                            'icon' => 'fa fa-trash fa-2x',
                            'attributes' => [
                                'rel' => 'tooltip',
                                'title' => $this->translator->trans('package.hover.disabled', [], 'NegasProjectTrans'),
                                'class' => 'btn btn-danger m-2',
                                'role' => 'button'
                            ]
                        ],[
                            'start_html' => '<div class="start_edit_action">',
                            'end_html' => '</div>',
                            'route' => 'admin_package_edit',
                            'route_parameters' => [
                                'id' => 'id'
                            ],
                            'icon' => 'fa fa-edit fa-2x',
                            'attributes' => [
                                'rel' => 'tooltip',
                                'title' => $this->translator->trans('package.hover.edit', [], 'NegasProjectTrans'),
                                'class' => 'btn btn-warning m-2',
                                'role' => 'button'
                            ]
                        ],
                    ]
                ]
            );
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
