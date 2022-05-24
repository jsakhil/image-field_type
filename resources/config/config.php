<?php

use Anomaly\ImageFieldType\Support\Config\FoldersHandler;

return [
    'folders'      => [
        'type'   => 'anomaly.field_type.checkboxes',
        'config' => [
            'handler' => FoldersHandler::class,
        ],
    ],
    'aspect_ratio' => [
        'type' => 'anomaly.field_type.text',
    ],
    'min_height'   => [
        'type'     => 'anomaly.field_type.integer',
        'required' => true,
        'config'   => [
            'default_value' => 400,
            'min'           => 200,
            'step'          => 50,
        ],
    ],
    'mode'         => [
        'required' => true,
        'type'     => 'anomaly.field_type.select',
        'config'   => [
            'options' => [
                'default' => 'anomaly.field_type.image::config.mode.option.default',
                'select'  => 'anomaly.field_type.image::config.mode.option.select',
                'upload'  => 'anomaly.field_type.image::config.mode.option.upload',
            ],
        ],
    ],
];
