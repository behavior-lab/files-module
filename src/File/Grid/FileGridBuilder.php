<?php namespace Anomaly\FilesModule\File\Grid;

use Anomaly\PreferencesModule\Preference\Contract\PreferenceRepositoryInterface;
use Anomaly\Streams\Platform\Ui\Grid\Grid;
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
    public function __construct(PreferenceRepositoryInterface $preferences, Grid $grid)
    {
        parent::__construct($grid);
        $default_order_by = $preferences->value('anomaly.module.files::default_order_by', 'updated_at');
        $default_order_direction = $preferences->value('anomaly.module.files::default_order_direction', 'desc');

        $this->options['order_by'] = [$default_order_by => $default_order_direction];
    }

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
