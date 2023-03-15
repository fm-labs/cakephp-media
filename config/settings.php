<?php
return [
    'Settings' => [
        'Media' => [
            'groups' => [
                'Media.Image' => [],
                'Media.Video' => [],
                'Media.Files' => [],
                'Media.Upload' => [],
                'Media.Debug' => [],
            ],
            'schema' => [
                'Media.Upload.enabled' => [
                    'group' => 'Media.Upload',
                    'type' => 'boolean',
                    'default' => true,
                ],
                'Media.debug' => [
                    'group' => 'Media.Debug',
                    'type' => 'boolean',
                    'default' => false,
                    'help' => __d('media', 'Show additional debug info on media elements')
                ],
            ],
        ],
    ],
];
