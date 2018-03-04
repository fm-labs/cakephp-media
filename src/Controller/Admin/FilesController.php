<?php

namespace Media\Controller\Admin;

use Media\Form\MediaUploadForm;
use Media\Lib\Media\MediaManager;
use Upload\Uploader;

class FilesController extends AppController
{
    /**
     * @var MediaManager
     */
    public $manager;

    public function initialize()
    {
        parent::initialize();
        $this->manager = MediaManager::getProvider('default');
    }

    public function index()
    {
        $path = ($this->request->query('path')) ?: '/';
        $path = rtrim($path, '/') . '/';
        $contents = $this->manager->read($path);

        list($folders, $files) = $contents;

        $this->set('path', $path);
        $this->set('folders', $folders);
        $this->set('files', $files);
    }

    public function upload()
    {
        $path = ($this->request->query('path')) ?: '/';
        $path = rtrim($path, '/') . '/';

        try {

            $uploader = new Uploader([
                'uploadDir' => $this->manager->getBasePath() . $path,
                'minFileSize' => 1,
                'maxFileSize' => 2097152, // 2MB
                'mimeTypes' => '*',
                'fileExtensions' => '*',
                'multiple' => false,
                'slug' => "_",
                'hashFilename' => false,
                'uniqueFilename' => false,
                'overwrite' => false,
                'saveAs' => null, // filename override
                //'pattern' => false, // @todo Implement me
            ]);

            $uploadForm = new MediaUploadForm('default', $uploader);
            if ($this->request->is('post')) {
                $uploadForm->execute($this->request->data);
            }
            $this->set('uploadForm', $uploadForm);

            //$uploader->setSaveAs('hellyea.jpg');
//            if ($this->request->is('post')) {
//                $upload = $uploader->upload($this->request->data['upload']);
//                debug($upload);
//            }

        } catch(\Exception $ex) {
            $error = $ex->getMessage();
            $this->Flash->error($error);
            return;
        }

    }
}
