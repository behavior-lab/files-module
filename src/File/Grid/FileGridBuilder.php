<?php namespace Anomaly\FilesModule\File\Grid;

use Anomaly\Streams\Platform\Ui\Grid\GridBuilder;

/**
 * Class FileTableBuilder
 *
 * @link          http://pyrocms.com/
 * @author        Behavior <support@behavior-lab.io>
 * @author        Claus Bube <chb@behavior-lab.io>
 */
class FileGridBuilder extends GridBuilder
{

    /**
     * The table filters.
     *
     * @var array
     */
    public $filters = [
        'search' => [
            'fields' => [
                'name',
                'keywords',
                'mime_type',
            ],
        ],
        'folder',
    ];

    /**
     * The table buttons.
     *
     * @var array
     */
    protected $buttons = [
        'edit',
        'view' => [
            'target' => '_blank',
        ],
    ];

}
