<?php

namespace Media\Controller\Admin;

use App\Controller\Admin\AppController as BaseAdminAppController;

class AppController extends BaseAdminAppController
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
