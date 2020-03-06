<?php

namespace Media\Controller\Admin;

use Cake\Core\Configure;
use Media\FileManager;
use Media\Form\MediaUploadForm;
use Media\Lib\Media\MediaManager;
use Upload\Uploader;

/**
 * @deprecated Use FilesController instead
 */
class MediaController extends AppController
{
    public function index()
    {
        $config = Configure::read('Media.default');
        $this->set('config', $config);

        try {
            $mgr = MediaManager::getProvider('default');

            $uploadForm = new MediaUploadForm('default', [
                'uploadDir' => $mgr->getBasePath(),
                'minFileSize' => 1,
                'maxFileSize' => 2097152, // 2MB
                'mimeTypes' => 'image/*',
                'fileExtensions' => 'gif,jpeg,png',
                'multiple' => false,
                'slug' => "_",
                'hashFilename' => false,
                'uniqueFilename' => false,
                'overwrite' => false,
                'saveAs' => null, // filename override
                //'pattern' => false, // @todo Implement me
            ]);
            if ($this->request->is('post')) {
                $uploadForm->execute($this->request->data);
            }
            $this->set('uploadForm', $uploadForm);
        } catch (\Exception $ex) {
            $error = $ex->getMessage();
            $this->Flash->error($error);
        }
    }

    public function upload($config = null)
    {
        try {
            $mgr = MediaManager::getProvider($config);

            $uploader = new Uploader([
                'uploadDir' => $mgr->getBasePath(),
                'minFileSize' => 1,
                'maxFileSize' => 2097152, // 2MB
                'mimeTypes' => 'image/*',
                'fileExtensions' => 'gif,jpeg,png',
                'multiple' => false,
                'slug' => "_",
                'hashFilename' => false,
                'uniqueFilename' => true,
                'overwrite' => false,
                'saveAs' => null, // filename override
                //'pattern' => false, // @todo Implement me
            ]);
        } catch (\Exception $ex) {
            $error = $ex->getMessage();
            $this->Flash->error($error);

            return;
        }

        //$uploader->setSaveAs('hellyea.jpg');

        if ($this->request->is('post')) {
            $upload = $uploader->upload($this->request->data['upload']);
            debug($upload);
        }
    }

    public function data($config = null)
    {
        $this->viewBuilder()->setClassName('Json');

        $config = ($config) ? $config : 'default';
        $path = ($this->request->getQuery('path')) ?: '/';
        $path = trim($path);
        $path = rtrim($path, '/');
        $path = $path . '/';
        $dirs = [];
        $files = [];
        $error = null;

        //MediaManager::config(Configure::read('Media'));

        try {
            $mgr = MediaManager::getProvider($config);
            list($dirs, $files) = $mgr->read($path);
        } catch (\Exception $ex) {
            $error = $ex->getMessage();
        }

        $this->set(compact('path', 'dirs', 'files', 'error'));
        $this->set('_serialize', ['path', 'dirs', 'files', 'error']);
    }
}
