<?php

namespace Media\Controller\Admin;

use Cake\Controller\Controller;

class AppController extends Controller
{

    public function initialize()
    {
        $this->loadComponent('Backend.Backend');
    }
}
