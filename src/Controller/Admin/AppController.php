<?php
declare(strict_types=1);

namespace Media\Controller\Admin;

use Cake\Controller\Controller;

class AppController extends Controller
{
    public function initialize(): void
    {
        $this->loadComponent('Admin.Admin');
    }
}
