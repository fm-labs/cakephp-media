<?php

namespace Media\Controller\Admin;

use Backend\Controller\Admin\AbstractBackendController;

class AppController extends AbstractBackendController
{

    public static function backendMenu()
    {
        return [
            'plugin.media' => [
                'title' => 'Media',
                'url' => ['plugin' => 'Media', 'controller' => 'MediaBrowser', 'action' => 'index'],
                'icon' => 'file image outline'
            ],
        ];
    }
}
