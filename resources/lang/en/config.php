<?php

return [
    'folders'      => [
        'name'         => 'Folders',
        'instructions' => 'Specify which folders are available for this field. Leave blank to display all folders.',
        'warning'      => 'Existing folder permissions take precedence over selected folders.',
    ],
    'aspect_ratio' => [
        'name'         => 'Aspect Ratio',
        'instructions' => 'Specify the crop area aspect ration like <strong>16:9</strong>, <strong>1:9</strong> or <strong>750:160</strong>.',
    ],
    'min_height'   => [
        'name'         => 'Minimum Height',
        'instructions' => 'Specify the minimum height of the crop area.',
    ],
    'mode'         => [
        'name'         => 'Input Mode',
        'instructions' => 'How should users provide image input?',
        'option'       => [
            'default' => 'Upload and/or select images.',
            'select'  => 'Select images only.',
            'upload'  => 'Upload images only.',
        ],
    ],
];
