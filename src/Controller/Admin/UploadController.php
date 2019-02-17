<?php

namespace Media\Controller\Admin;

use Upload\Uploader;

class UploadController extends AppController
{
    public function index()
    {
        $uploader = new Uploader([
            'uploadDir' => MEDIA . 'uploads/',
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
        //$uploader->setSaveAs('hellyea.jpg');

        if ($this->request->is('post')) {
            $upload = $uploader->upload($this->request->data['upload']);
            debug($upload);
        }
    }
}
