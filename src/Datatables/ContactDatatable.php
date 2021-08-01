<?php

namespace App\Datatables;

use App\Entity\Contact;
use App\Entity\User;
use Closure;
use Sg\DatatablesBundle\Datatable\AbstractDatatable;
use Sg\DatatablesBundle\Datatable\Column\ActionColumn;
use Sg\DatatablesBundle\Datatable\Column\Column;
use Sg\DatatablesBundle\Datatable\Column\DateTimeColumn;
use Sg\DatatablesBundle\Datatable\Column\VirtualColumn;
use Sg\DatatablesBundle\Datatable\Style;

class ContactDatatable extends AbstractDatatable
{
    public function getLineFormatter(): Closure
    {
        return function (&$row) {
            $contact = $this->em->getRepository(Contact::class)->find($row["id"]);
            /** @var User $user */
            $user = $contact->getUser();
            $row['userFullName'] = $user->getFullname();
        };
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
            ->add("id", Column::class, [
                "visible" => false
            ])
            ->add("userFullName", VirtualColumn::class, [
                "title" => $this->translator->trans('contact.label.fullnameUser', [], 'NegasProjectTrans'),
            ])
            ->add("user.email", Column::class, [
                "title" => $this->translator->trans('contact.label.emailUser', [], 'NegasProjectTrans'),
            ])
            ->add("subject", Column::class, [
                "title" => $this->translator->trans('contact.label.subject', [], 'NegasProjectTrans'),
            ])
            ->add("message", Column::class, [
                "title" => $this->translator->trans('contact.label.message', [], 'NegasProjectTrans'),

            ])
            ->add("createdAt", DateTimeColumn::class, [
                "title" => $this->translator->trans('contact.label.createdAt', [], 'NegasProjectTrans'),
            ])
            ->add(null, ActionColumn::class, [
                    "title" => $this->translator->trans('contact.label.action', [], 'NegasProjectTrans'),
                    'start_html' => '<div class="start_actions" style="width:150px; text-align: center; display: flex; justify-content: space-between">',
                    "width" => "200px",
                    'end_html' => '</div>',
                    'actions' => [
                        [
                            'route' => 'admin_contact_show',
                            'label' => null,
                            'route_parameters' => [
                                'id' => 'id',
                                '_format' => 'html',
                                '_locale' => 'fr'
                            ],
                            'icon' => 'fas fa-eye fa-2x',
                            'attributes' => [
                                'rel' => 'tooltip',
                                'title' => $this->translator->trans('contact.hover.show', [], 'NegasProjectTrans'),
                                'role' => 'button',
                                'class' => "btn btn-danger btn-sm m-2"
                            ],
                            'start_html' => '<div class="start_show_action">',
                            'end_html' => '</div>',
                        ],[
                            'route' => 'admin_contact_reply',
                            'label' => null,
                            'route_parameters' => [
                                'id' => 'id',
                                '_format' => 'html',
                                '_locale' => 'fr'
                            ],
                            'icon' => 'fas fa-reply fa-2x',
                            'attributes' => [
                                'rel' => 'tooltip',
                                'title' => $this->translator->trans('contact.hover.reply', [], 'NegasProjectTrans'),
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

    /**
     * @inheritDoc
     */
    public function getEntity()
    {
        return Contact::class;
    }

    /**
     * @inheritDoc
     */
    public function getName()
    {
        return "contact_datatable";
    }
}
