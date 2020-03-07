<?php

namespace Media\Controller\Admin;

use Cake\Core\Plugin;
use Upload\Uploader;

class UploadController extends AppController
{
    public function index()
    {
        if (!Plugin::isLoaded('Upload')) {
            $this->Flash->error(__("Plugin `{0}` not installed", 'Upload'));
            $this->redirect($this->referer('/'));
        }

        $uploader = new Uploader([
            'uploadDir' => MEDIA . 'uploads/',
            'minFileSize' => 1,
            'maxFileSize' => 2097152, // 2MB
            'mimeTypes' => 'image/*',
            'fileExtensions' => 'gif,jpeg,jpg,png',
            'multiple' => false,
            'slug' => "_",
            'hashFilename' => false,
            'uniqueFilename' => true,
            'overwrite' => false,
            'saveAs' => null, // filename override
            //'pattern' => false, // @todo Implement me
        ]);
        //$uploader->setSaveAs('hellyea.jpg');

        if ($this->request->is('post')) {
            $upload = $uploader->upload($this->request->getData('upload'));
            $this->set(compact('upload'));
        }
    }
}
