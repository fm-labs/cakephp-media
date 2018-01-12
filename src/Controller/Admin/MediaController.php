<?php

namespace Media\Controller\Admin;

use Cake\Core\Configure;
use Media\FileManager;
use Media\Lib\Media\MediaManager;

class MediaController extends AppController
{
    public function index()
    {
        $config = Configure::read('Media.default');
        $this->set('config', $config);
    }

    public function data($config = null)
    {
        $this->viewBuilder()->className('Json');

        $config = ($config) ?? 'default';
        $path = ($this->request->query('path')) ?? '/';
        $path = trim($path);
        $path = rtrim($path, '/');
        $path = $path . '/';
        $dirs = [];
        $files = [];
        $error = null;

        MediaManager::config(Configure::read('Media'));

        try {
            $mgr = MediaManager::getProvider($config);
            list($dirs, $files) = $mgr->read($path);

        } catch(\Exception $ex) {
            $error = $ex->getMessage();
        }

        $this->set(compact('path', 'dirs', 'files', 'error'));
        $this->set('_serialize', ['path', 'dirs', 'files', 'error']);
    }
}
