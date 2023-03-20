<?php namespace Anomaly\FilesModule\File\Table;

use Anomaly\PreferencesModule\Preference\Contract\PreferenceRepositoryInterface;
use Anomaly\Streams\Platform\Ui\Table\Table;
use Anomaly\Streams\Platform\Ui\Table\TableBuilder;

/**
 * Class FileTableBuilder
 *
 * @link          http://pyrocms.com/
 * @author        PyroCMS, Inc. <support@pyrocms.com>
 * @author        Ryan Thompson <ryan@pyrocms.com>
 */
class FileTableBuilder extends TableBuilder
{
    public function __construct(PreferenceRepositoryInterface $preferences, Table $table)
    {
        parent::__construct($table);
        $default_order_by = $preferences->value('anomaly.module.files::default_order_by', 'updated_at');
        $default_order_direction = $preferences->value('anomaly.module.files::default_order_direction', 'desc');

        $this->options['order_by'] = [$default_order_by => $default_order_direction];
    }

    /**
     * The table views.
     *
     * @var array
     */
    protected $views = [
        'all',
        'recently_created' => [
            'text'    => 'streams::view.newest',
            'columns' => [
                'entry.preview'             => [
                    'heading' => 'anomaly.module.files::field.preview.name',
                ],
                'name'                      => [
                    'sort_column' => 'name',
                    'wrapper'     => '
                    <strong>{value.file}</strong>
                    <br>
                    <small class="text-muted">{value.disk}://{value.folder}/{value.file}</small>
                    <br>
                    <span>{value.size} {value.keywords}</span>',
                    'value'       => [
                        'file'     => 'entry.name',
                        'folder'   => 'entry.folder.slug',
                        'keywords' => 'entry.keywords.labels|join',
                        'disk'     => 'entry.folder.disk.slug',
                        'size'     => 'entry.size_label',
                    ],
                ],
                'size'                      => [
                    'sort_column' => 'size',
                    'value'       => 'entry.readable_size',
                ],
                'mime_type',
                'folder',
                'entry.created_at_datetime' => [
                    'heading'     => 'streams::entry.created_at',
                    'sort_column' => 'created_at',
                ],
            ],
        ],
        'trash'            => [
            'columns' => [
                'name',
                'size',
                'mime_type',
            ],
        ],
    ];

    /**
     * The table filters.
     *
     * @var array
     */
    protected $filters = [
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
     * The table columns.
     *
     * @var array
     */
    protected $columns = [
        'entry.preview' => [
            'heading' => 'anomaly.module.files::field.preview.name',
        ],
        'name'          => [
            'sort_column' => 'name',
            'wrapper'     => '
                    <strong>{value.file}</strong>
                    <br>
                    <small class="text-muted">{value.disk}://{value.folder}/{value.file}</small>
                    <br>
                    <span><span class="tag {value.size_tag_type} tag-sm">{value.readable_file_size}</span> <span class="tag tag-default tag-sm text-gray-dark">{value.mime_type}</span> {value.size} {value.keywords}</span>',
            'value'       => [
                'file'     => 'entry.name',
                'mime_type'     => 'entry.mime_type',
                'folder'   => 'entry.folder.slug',
                'keywords' => 'entry.keywords.labels|join',
                'disk'     => 'entry.folder.disk.slug',
                'size'     => 'entry.size_label',
                'size_tag_type' => 'entry.size.value < 150000 ? "tag-info" : entry.size.value < 350000 ? "tag-warning" : "tag-danger"',
                'readable_file_size' => 'entry.readable_size',
            ],
        ],
        'folder',
        'updated_at'          => [
            'sort_column' => 'updated_at',
            'wrapper'     => '<span class="text-nowrap">{value.updated_at_human}</span>
                              <br>
                              <small class="text-muted text-nowrap">{value.updated_at}</small>',
            'value'       => [
                'updated_at' => 'entry.updated_at.format("M jS, Y, H:i")',
                'updated_at_human' => 'entry.updated_at.diffForHumans()'
            ],
        ],
        'is_usage_known'          => [
            'value'       => 'entry.isUsed() is same as (true) ? "Yes" : entry.isUsed() is same as (false) ? "No" : "Please active file usage first"',
        ],
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

    /**
     * The table buttons.
     *
     * @var array
     */
    protected $actions = [
        'delete',
        'edit',
        'move' => [
            'tag'         => 'a',
            'type'        => 'info',
            'icon'        => 'upload',
            'data-toggle' => 'modal',
            'data-target' => '#modal',
            'href'        => 'admin/files/move/choose',
        ],
    ];
}
