<?php

namespace Media\Controller\Admin;

use Cake\Controller\Controller;

class AppController extends Controller
{

    public function initialize()
    {
        $this->loadComponent('Backend.Backend');
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
