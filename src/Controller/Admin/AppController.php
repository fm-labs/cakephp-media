<?php

namespace Media\Controller\Admin;

use App\Controller\Admin\AppController as BaseAdminAppController;

class AppController extends BaseAdminAppController
{

    public function initialize()
    {
        parent::initialize();

        //$this->viewBuilder()->layout('media');
    }

    public static function backendMenu()
    {
        return [
            'plugin.media' => [
                'title' => 'Media',
                'url' => ['plugin' => 'Media', 'controller' => 'MediaBrowser', 'action' => 'index'],
                'data-icon' => 'file image outline'
            ],
        ];
    }
}
