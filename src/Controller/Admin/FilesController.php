<?php
declare(strict_types=1);

namespace Media\Controller\Admin;

use Cake\Filesystem\File;
use Media\Form\MediaUploadForm;
use Media\Lib\Media\MediaManager;
use Media\Model\Entity\MediaFile;
use Upload\Uploader;

class FilesController extends AppController
{
    /**
     * @var \Media\Lib\Media\MediaManager
     */
    protected $_manager;

    /**
     * @inheritDoc
     */
    public function initialize(): void
    {
        parent::initialize();
    }

    /**
     * @return \Media\Lib\Media\MediaManager
     * @throws \Exception
     */
    public function getMediaManager(): MediaManager
    {
        if (!$this->_manager) {
            $this->_manager = MediaManager::get('default');
        }

        return $this->_manager;
    }

    /**
     * @return void
     */
    public function index(): void
    {
        $path = $this->request->getQuery('path', '/');
        $path = rtrim($path, '/') . '/';
        $file = $this->request->getQuery('file');

        $manager = null;
        $folders = $files = [];
        try {
            $manager = $this->getMediaManager();
            $contents = $manager->read($path);
            [$folders, $files] = $contents;

            if ($file) {
                $f = $this->_getFileFromRequest();
                $mf = MediaFile::fromFile($f);
                $this->set('selectedFile', $f);
                $this->set('mediaFile', $mf);
            }
        } catch (\Exception $ex) {
            $this->Flash->error($ex->getMessage());
        }


        $this->set('path', $path);
        $this->set('folders', $folders);
        $this->set('files', $files);
        $this->set('manager', $manager);
    }

    /**
     * @return void
     */
    public function view(): void
    {
        $f = $this->_getFileFromRequest();
        $mf = MediaFile::fromFile($f);
        $this->set('selectedFile', $f);
        $this->set('mediaFile', $mf);
        $contents = null;

        if (!$f->exists() || !$f->readable()) {
            $this->Flash->error('File does not exist or is not readable by the webserver');
            //$this->redirect($this->referer(['action' => 'index']));
        } else {
            $ext = strtolower($f->ext());
            if (!in_array($ext, ['txt', 'md', 'conf', 'html', 'json', 'xml'])) {
                $this->Flash->warning('This file type can not be viewed');
                //$this->redirect($this->referer(['action' => 'index']));
            } else {
                $contents = $f->read();
            }
        }

        $this->set('contents', $contents);
    }

    /**
     * @return void
     */
    public function edit(): void
    {
        $f = $this->_getFileFromRequest();
        $this->set('selectedFile', $f);

        //@TODO Implement me
        $this->Flash->warning('This file can not be edited');
        $this->redirect($this->referer(['action' => 'index']));
    }

    public function rename(): void
    {
        $f = $this->_getFileFromRequest();
        $this->set('selectedFile', $f);

        if ($this->getRequest()->is(['post'])) {
            $path = $this->getRequest()->getData('path');
            $newName = $this->getRequest()->getData('newName');
            $info = pathinfo($path);
            $newPath = sprintf('%s/%s.%s', $info['dirname'], $newName, $info['extension']);
            if (copy($path, $newPath)) {
                unlink($path);
                $this->Flash->success(__d('media', 'File renamed from {0} to {1}', $info['filename'], $newName));
                $this->redirect($this->referer(['action' => 'index']));
            } else {
                $this->Flash->error('This file can not be renamed');
            }
        }
    }

    /**
     * @return void
     */
    public function delete(): void
    {
        $f = $this->_getFileFromRequest();
        if (!$f->exists()) {
            $this->Flash->error('File does not exist');
        }

        if ($f->delete()) {
            $this->Flash->success('File deleted');
        } else {
            $this->Flash->error('Failed to delete file');
        }

        $this->redirect($this->referer(['action' => 'index']));
    }

    /**
     * @return \Cake\Filesystem\File
     * @deprecated
     */
    protected function _getFileFromRequest(): File
    {
        $basePath = $this->getMediaManager()->getBasePath();
        //@TODO Sanitize query!
        $path = $this->request->getQuery('path') ?: '/';
        $path = rtrim($path, '/') . '/';
        $file = $this->request->getQuery('file');

        $f = new File($basePath . $path . $file);

        return $f;
    }

    /**
     * @return void
     */
    public function upload(): void
    {
        $path = $this->request->getQuery('path') ?: '/';
        $path = trim($path, '/') . '/';

        try {
            // Uploader
            // @TODO Read media uploader params from configuration
            $uploader = new Uploader([
                'minFileSize' => 1,
                'maxFileSize' => 20 * 1024 * 1024, // 2MB
                'mimeTypes' => '*',
                'fileExtensions' => '*',
                'multiple' => false,
                'slug' => '_',
                'hashFilename' => false,
                'uniqueFilename' => false,
                'overwrite' => false,
                'saveAs' => null, // filename override
                #'pattern' => false, // @todo Implement me
            ]);
            $uploader->setUploadDir($this->getMediaManager()->getBasePath() . $path);

            // UploadForm
            $uploadForm = new MediaUploadForm('default', $uploader);
            if ($this->request->is('post')) {
                $uploadForm->execute($this->request->getData());
            }
            $this->set('uploadForm', $uploadForm);
        } catch (\Exception $ex) {
            $error = $ex->getMessage();
            $this->Flash->error($error);
        }
    }
}
