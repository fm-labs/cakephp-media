<?php
return [
    'Settings' => [
        'Media' => [
            'groups' => [
                'Media.Image' => [],
                'Media.Video' => [],
                'Media.Files' => [],
                'Media.Upload' => [],
            ],
            'schema' => [
                'Media.Upload.enabled' => [
                    'group' => 'Media.Upload',
                    'type' => 'boolean',
                    'default' => true,
                ],
            ],
        ],
    ],
];
