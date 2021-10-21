<?php namespace Anomaly\FilesModule;

use Anomaly\PreferencesModule\Preference\Contract\PreferenceRepositoryInterface;
use Anomaly\Streams\Platform\Ui\ControlPanel\ControlPanelBuilder;

/**
 * Class PagesModuleSections
 *
 * @link   http://pyrocms.com/
 * @author PyroCMS, Inc. <support@pyrocms.com>
 * @author Ryan Thompson <ryan@pyrocms.com>
 */
class FilesModuleSections
{

    /**
     * Handle the sections.
     *
     * @param ControlPanelBuilder           $builder
     * @param PreferenceRepositoryInterface $preferences
     */
    public function handle(ControlPanelBuilder $builder, PreferenceRepositoryInterface $preferences)
    {
        $view = $preferences->value('anomaly.module.files::file_view', 'grid');

        $builder->setSections(
            [
                'files'   => [
                    'buttons' => [
                        'upload' => [
                            'data-toggle' => 'modal',
                            'icon'        => 'upload',
                            'data-target' => '#modal',
                            'type'        => 'success',
                            'href'        => 'admin/files/upload/choose',
                        ],
                        'change_view' => [
                            'type'    => 'info',
                            'enabled' => 'admin/files',
                            'icon'    => ($view == 'grid' ? 'fas fa-table' : 'fas fa-th'),
                            'href'    => 'admin/files/change/' . ($view == 'grid' ? 'table' : 'grid'),
                            'text'    => 'anomaly.module.files::button.' . ($view == 'grid' ? 'table_view' : 'grid_view'),
                        ],
                    ],
                ],
                'folders' => [
                    'buttons'  => [
                        'new_folder',
                    ],
                    'sections' => [
                        'assignments' => [
                            'hidden'  => true,
                            'href'    => 'admin/files/folders/assignments/{request.route.parameters.stream}',
                            'buttons' => [
                                'assign_fields' => [
                                    'data-toggle' => 'modal',
                                    'data-target' => '#modal',
                                    'href'        => 'admin/files/folders/assignments/{request.route.parameters.stream}/choose',
                                ],
                            ],
                        ],
                    ],
                ],
                'disks'   => [
                    'buttons' => [
                        'new_disk' => [
                            'data-toggle' => 'modal',
                            'data-target' => '#modal',
                            'href'        => 'admin/files/disks/choose',
                        ],
                    ],
                ],
                'fields'  => [
                    'buttons' => [
                        'new_field' => [
                            'data-toggle' => 'modal',
                            'data-target' => '#modal',
                            'href'        => 'admin/files/fields/choose',
                        ],
                    ],
                ],
            ]
        );
    }
}
