<?php

return [
    'file_view' => [
        'name'   => 'File View',
        'option' => [
            'grid'  => 'Grid View',
            'table' => 'Table View',
        ],
    ],
    'default_order_by' => [
        'name'   => 'Default sorting',
        'option' => [
            'name'  => 'Name',
            'created_at' => 'Created at',
            'updated_at' => 'Updated at',
            'mime_type' => 'Mime type',
            'size' => 'Size',
        ],
    ],
    'default_order_direction' => [
        'name'   => 'Default sort direction',
        'option' => [
            'asc'  => 'Ascending order',
            'desc' => 'Descending order',
        ],
    ],
];
