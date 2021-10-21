<?php

return [
    'file_view' => [
        'type'   => 'anomaly.field_type.select',
        'config' => [
            'options' => [
                'tree'  => 'anomaly.module.files::preferences.file_view.option.tree',
                'table' => 'anomaly.module.files::preferences.file_view.option.table',
            ],
        ],
    ],
];
