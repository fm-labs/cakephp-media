<?php
return [
    'Media' => [
        'Files' => [
            'default' => [
                'label' => 'All Media',
                'provider' => 'Media.LocalStorage',
                'basePath' => MEDIA, //WWW_ROOT . 'media' . DS,
                'baseUrl' => MEDIA_URL, //'/media',
                'public' => true,
                'upload' => 'default',
            ],
            /*
            'gallery' => [
                'label' => 'Products Gallery',
                'provider' => 'Media.LocalStorage',
                'basePath' => WWW_ROOT . 'media' . DS . 'gallery' . DS,
                'public' => true,
                'baseUrl' => '/media/gallery',
            ],
            'admin' => [
                'label' => 'Admin Files',
                'provider' => 'Media.LocalStorage',
                'basePath' => DATA . 'admin' . DS,
                'public' => false,
                'baseUrl' => false,
            ],
            */
        ],
        'Upload' => [
            'files' => [
                'uploadDir' => null,
                'minFileSize' => 1,
                'maxFileSize' => 2097152, // 2MB
                'mimeTypes' => '*', //['image/*'],
                'fileExtensions' => '*',
                'multiple' => false, //@TODO Multiple file upload
                'slug' => '_',
                'hashFilename' => false,
                'uniqueFilename' => false,
                'overwrite' => false,
                'saveAs' => null, // filename override
                //'pattern' => false, // @todo Implement me
            ]
        ]
    ],
];
