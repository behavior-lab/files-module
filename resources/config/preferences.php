<?php

return [
    'file_view' => [
        'type'   => 'anomaly.field_type.select',
        'config' => [
            'options' => [
                'tree'  => 'anomaly.module.files::preference.file_view.option.tree',
                'table' => 'anomaly.module.files::preference.file_view.option.table',
            ],
        ],
    ],
    'default_order_by' => [
        'type'   => 'anomaly.field_type.select',
        'config' => [
            'options' => [
                'name'  => 'anomaly.field_type.files::preference.default_order_by.option.name',
                'created_at' => 'anomaly.field_type.files::preference.default_order_by.option.created_at',
                'updated_at' => 'anomaly.field_type.files::preference.default_order_by.option.updated_at',
                'mime_type' => 'anomaly.field_type.files::preference.default_order_by.option.mime_type',
                'size' => 'anomaly.field_type.files::preference.default_order_by.option.size',
            ],
        ],
    ],
    'default_order_direction' => [
        'type'   => 'anomaly.field_type.select',
        'config' => [
            'options' => [
                'ASC'  => 'anomaly.field_type.files::preference.default_order_direction.option.asc',
                'DESC'  => 'anomaly.field_type.files::preference.default_order_direction.option.desc',
            ],
        ],
    ],
];
