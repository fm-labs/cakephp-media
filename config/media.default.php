<?php
return [
    'Media' => [
        'default' => [
            'label' => 'All Media',
            'provider' => 'Media.LocalStorage',
            'path' => WWW_ROOT . 'media' . DS,
            'public' => true,
            'url' => '/media',
        ],
        /*
        'gallery' => [
            'label' => 'Products Gallery',
            'provider' => 'Media.LocalStorage',
            'path' => WWW_ROOT . 'media' . DS . 'gallery' . DS,
            'public' => true,
            'url' => '/media/gallery',
        ],
        'admin' => [
            'label' => 'Admin Files',
            'provider' => 'Media.LocalStorage',
            'path' => DATA . 'admin' . DS,
            'public' => false,
            'url' => false,
        ],
        */
    ]
];
