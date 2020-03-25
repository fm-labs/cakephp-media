<?php

namespace Media\Controller\Admin;

use Cake\Filesystem\File;
use Media\Form\MediaUploadForm;
use Media\Lib\Media\MediaManager;
use Media\Model\Entity\MediaFile;
use Upload\Uploader;

class FilesController extends AppController
{
    /**
     * @var MediaManager
     */
    public $manager;

    public function initialize(): void
    {
        parent::initialize();
        $this->manager = MediaManager::get('default');
    }

    public function index()
    {
        $path = ($this->request->getQuery('path')) ?: '/';
        $path = rtrim($path, '/') . '/';
        $contents = $this->manager->read($path);
        list($folders, $files) = $contents;

        if ($this->request->getQuery('file')) {
            $f = $this->_getFileFromRequest();
            $this->set('selectedFile', $f);
        }

        $this->set('path', $path);
        $this->set('folders', $folders);
        $this->set('files', $files);
        $this->set('manager', $this->manager);
    }

    public function view()
    {
        $f = $this->_getFileFromRequest();
        $this->set('selectedFile', $f);
        $contents = null;

        if (!$f->exists() || !$f->readable()) {
            $this->Flash->error("File does not exist or is not readable by the webserver");
            //$this->redirect($this->referer(['action' => 'index']));
        } else {
            $ext = strtolower($f->ext());
            if (!in_array($ext, ['txt', 'md', 'conf', 'html', 'json', 'xml'])) {
                $this->Flash->warning("This file type can not be viewed");
                //$this->redirect($this->referer(['action' => 'index']));
            } else {
                $contents = $f->read();
            }
        }

        $this->set('contents', $contents);
    }

    public function edit()
    {
        $f = $this->_getFileFromRequest();
        $this->set('selectedFile', $f);

        //@TODO Implement me
        $this->Flash->warning("This file can not be edited");
        $this->redirect($this->referer(['action' => 'index']));
    }

    public function delete()
    {
        $f = $this->_getFileFromRequest();
        if (!$f->exists()) {
            $this->Flash->error("File does not exist");
        }

        if ($f->delete()) {
            $this->Flash->success("File deleted");
        } else {
            $this->Flash->error("Failed to delete file");
        }

        $this->redirect($this->referer(['action' => 'index']));
    }

    protected function _getFileFromRequest()
    {
        $basePath = $this->manager->getBasePath();
        //@TODO Sanitize query!
        $path = ($this->request->getQuery('path')) ?: '/';
        $path = rtrim($path, '/') . '/';
        $file = $this->request->getQuery('file');

        $f = new File($basePath . $path . $file);

        return $f;
    }

    /*
    public function upload()
    {
        $path = ($this->request->getQuery('path')) ?: '/';
        $path = trim($path, '/') . '/';

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
                $uploadForm->execute($this->request->getData());
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
    */
}
