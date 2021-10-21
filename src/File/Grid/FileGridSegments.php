<?php namespace Anomaly\FilesModule\File\Grid;

use Anomaly\FilesModule\File\Contract\FileInterface;

/**
 * Class PageTreeSegments
 *
 * @link          http://pyrocms.com/
 * @author        Behavior <support@behavior-lab.io>
 * @author        Claus Bube <chb@behavior-lab.io>
 */
class FileGridSegments
{

    /**
     * Handle the tree segments.
     *
     * @param FileGridBuilder $builder
     */
    public function handle(FileGridBuilder $builder)
    {
        $builder->getGridOption('item_value', 'entry.name');
        $builder->setSegments(
            [
                'title' => [
                    'href' => 'admin/pages/edit/{entry.id}',
                ],
                [
                    'class' => 'text-faded',
                    'value' => function (FileInterface $entry) {
                        return '<span class="small" style="padding-right:10px;">' . $entry->type->name . '</span>';
                    },
                ],
                [
                    'data-toggle' => 'tooltip',
                    'class'       => 'text-success',
                    'value'       => '<i class="fa fa-home"></i>',
                    'attributes'  => [
                        'title' => 'module::message.home',
                    ],
                    'enabled'     => function (FileInterface $entry) {
                        return $entry->isHome();
                    },
                ],
                [
                    'data-toggle' => 'tooltip',
                    'class'       => 'text-muted',
                    'value'       => '<i class="fa fa-chain-broken"></i>',
                    'attributes'  => [
                        'title' => 'module::message.hidden',
                    ],
                    'enabled'     => function (FileInterface $entry) {
                        return !$entry->isVisible();
                    },
                ],
                [
                    'data-toggle' => 'tooltip',
                    'class'       => 'text-muted',
                    'value'       => '<i class="fa fa-lock"></i>',
                    'attributes'  => [
                        'title' => 'module::message.restricted',
                    ],
                    'enabled'     => function (FileInterface $entry) {

                        $roles = $entry->getAllowedRoles();

                        return !$roles->isEmpty();
                    },
                ],
                [
                    'data-toggle' => 'tooltip',
                    'class'       => 'text-danger',
                    'value'       => '<i class="fa fa-ban"></i>',
                    'attributes'  => [
                        'title' => 'module::message.disabled',
                    ],
                    'enabled'     => function (FileInterface $entry) {
                        return !$entry->isEnabled();
                    },
                ],
                [
                    'data-toggle' => 'tooltip',
                    'class'       => 'text-info',
                    'value'       => '<i class="fa fa-clock-o"></i>',
                    'attributes'  => [
                        'title' => 'module::message.scheduled',
                    ],
                    'enabled'     => function (FileInterface $entry) {
                        return !$entry->isPublished();
                    },
                ],
            ]
        );
    }
}
