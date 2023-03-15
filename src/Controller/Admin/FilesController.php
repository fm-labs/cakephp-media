<?php
declare(strict_types=1);

namespace Media\Controller\Admin;

use Cake\Event\EventInterface;
use Cake\Filesystem\File;
use Cake\Filesystem\Folder;
use Cake\Http\Exception\BadRequestException;
use Media\Form\DeleteFileForm;
use Media\Form\MediaUploadForm;
use Media\Form\NewFileForm;
use Media\Form\NewFolderForm;
use Media\Form\RenameFileForm;
use Media\Form\RenameFolderForm;
use Media\MediaException;
use Media\MediaManager;
use Media\Model\Entity\MediaFile;
use Upload\Uploader;

/**
 * @property string $config Selected media config
 * @property string $path Selected directory path
 * @property string $file Selected file name
 */
class FilesController extends AppController
{
    /**
     * @var \Media\MediaManager|null
     */
    protected ?MediaManager $_manager = null;

    /**
     * @inheritDoc
     */
    public function initialize(): void
    {
        parent::initialize();
    }

    /**
     * @return \Media\MediaManager
     * @throws \Exception
     */
    public function getMediaManager(): MediaManager
    {
        if (!$this->_manager) {
            $this->_manager = MediaManager::get($this->config);
        }

        return $this->_manager;
    }

    public function beforeFilter(EventInterface $event)
    {
        parent::beforeFilter($event);

        $this->config = $this->request->getParam('config');
        $this->path = trim($this->request->getQuery('path', '/'), '/') . '/';
        $this->file = $this->request->getQuery('file');

        if (!$this->config) {
            throw new BadRequestException("No media config selected");
        }
    }

    public function beforeRender(EventInterface $event)
    {
        parent::beforeRender($event);

        $this->set('config', $this->config);
        $this->set('path', $this->path);
        $this->set('file', $this->file);
        $this->set('manager', $this->_manager);
    }

    /**
     * @return void
     */
    public function index(): void
    {
    }

    /**
     * @return void
     */
    public function view(): void
    {
        $path = $this->path;
        $file = $this->file;
        $contents = null;

        try {
            $manager = $this->getMediaManager();

            if (!$manager->fileExists($path . $file)) {
                throw new MediaException(__d('media', 'File does not exist'));
            }

            if ($file) {
                $mf = MediaFile::fromPath($path . $file, $this->config);
                $this->set('selectedFile', $mf);

                $ext = '';
                //$ext = strtolower($mf->ext());
                if (!in_array($ext, ['txt', 'md', 'conf', 'html', 'json', 'xml'])) {
                    $this->Flash->warning(__d('media', 'This file type can not be viewed'));
                    //$this->redirect($this->referer(['action' => 'index']));
                } else {
                    //$contents = $manager->readFile($path . $file);
                }
            }
        } catch (\Exception $ex) {
            $this->Flash->error($ex->getMessage());
        }

        $this->set('contents', $contents);
    }

    /**
     * @return \Cake\Http\Response|null
     */
    public function edit(): ?\Cake\Http\Response
    {
        //@TODO Implement me
        $this->Flash->warning(__d('media', 'File editing is not supported yet'));
        return $this->redirect([
            'action' => 'index',
            'config' => $this->config,
            '?' => ['path' => $this->path]
        ]);
    }

    /**
     * @return \Cake\Http\Response|null
     * @throws \Exception
     */
    public function renameFolder(): ?\Cake\Http\Response
    {
        try{
            $form = new RenameFolderForm();
            if ($this->getRequest()->is(['put', 'post'])) {
                if (!$form->execute($this->getRequest()->getData())) {
                    throw new MediaException(__d('media', 'Failed to rename folder'));
                }

                $this->Flash->success(__d('media', 'Folder renamed'));
                return $this->redirect([
                    'action' => 'index',
                    'config' => $this->config,
                    '?' => ['path' => $this->path]
                ]);
            }
            $this->set('form', $form);
        } catch (\Exception $ex) {
            $this->Flash->error($ex->getMessage());
            return $this->redirect([
                'action' => 'index',
                'config' => $this->config,
                '?' => ['path' => $this->path]
            ]);
        }

        return null;
    }

    /**
     * @return \Cake\Http\Response|null
     * @throws \Exception
     */
    public function rename(): ?\Cake\Http\Response
    {
        try{
            $form = new RenameFileForm();
            if ($this->getRequest()->is(['put', 'post'])) {
                if (!$form->execute($this->getRequest()->getData())) {
                    throw new MediaException(__d('media', 'Failed to rename file'));
                }

                $this->Flash->success(__d('media', 'File renamed'));
                return $this->redirect([
                    'action' => 'index',
                    'config' => $this->config,
                    '?' => ['path' => $this->path]
                ]);
            }
            $this->set('form', $form);
        } catch (\Exception $ex) {
            $this->Flash->error($ex->getMessage());
            return $this->redirect([
                'action' => 'index',
                'config' => $this->config,
                '?' => ['path' => $this->path]
            ]);
        }

        return null;
    }

    /**
     * @return \Cake\Http\Response|null
     * @throws \Exception
     */
    public function delete(): ?\Cake\Http\Response
    {
        try{
            $form = new DeleteFileForm();
            if ($this->getRequest()->is(['put', 'post'])) {
                if (!$form->execute($this->getRequest()->getData())) {
                    throw new MediaException(__d('media', 'Failed to delete file'));
                }

                $this->Flash->success(__d('media', 'File deleted'));
                return $this->redirect([
                    'action' => 'index',
                    'config' => $this->config,
                    '?' => ['path' => $this->path]
                ]);
            }
            $this->set('form', $form);
        } catch (\Exception $ex) {
            $this->Flash->error($ex->getMessage());
            return $this->redirect([
                'action' => 'index',
                'config' => $this->config,
                '?' => ['path' => $this->path]
            ]);
        }

        return null;
    }

    /**
     * @return \Cake\Http\Response|null
     */
    public function upload(): ?\Cake\Http\Response
    {
        $path = $this->path;
        $uploadForm = null;

        $this->set('_FILES', $_FILES);

        try {
            // Uploader
            // @TODO Read media uploader params from configuration
            $multiple = true;
            $uploader = new Uploader([
                'minFileSize' => 1,
                'maxFileSize' => 20 * 1024 * 1024, // 2MB
                'mimeTypes' => '*',
                'fileExtensions' => '*',
                'multiple' => $multiple,
                'slug' => '_',
                'hashFilename' => false,
                'uniqueFilename' => false,
                'overwrite' => false,
                'saveAs' => null, // filename override
            ]);
            $uploader->setUploadDir($this->getMediaManager()->getBasePath() . $path);
            $this->set('uploadConfig', $uploader->getConfig());
            $this->set('uploadMultiple', $multiple);

            // UploadForm
            $uploadForm = new MediaUploadForm('default', $uploader);
            $this->set('form', $uploadForm);

            if ($this->request->is('post')) {
                $uploadedFiles = $this->request->getUploadedFiles();
                if ($uploadForm->execute($uploadedFiles)) {
                    $this->Flash->success(__d('media', "Upload successful"));
                    return $this->redirect([
                        'action' => 'index',
                        'config' => $this->config,
                        '?' => ['path' => $this->path]
                    ]);
                }

                //$upload = $uploadForm->getUploadedFiles();
                //$this->set('upload', $upload);
            }
        } catch (\Exception $ex) {
            $error = $ex->getMessage();
            $this->Flash->error($error);
        }

        return null;
    }

    /**
     * @return \Cake\Http\Response|null
     * @throws \Exception
     */
    public function newFolder(): ?\Cake\Http\Response
    {
        if (!$this->getMediaManager()->directoryExists($this->path)) {
            $this->Flash->error(__d('media', 'Folder does not exist: {0}', $this->path));
            $this->redirect(['action' => 'index']);
        }

        try{
            $form = new NewFolderForm();
            if ($this->getRequest()->is(['put', 'post'])) {
                if (!$form->execute($this->getRequest()->getData())) {
                    throw new MediaException(__d('media', 'Failed to create folder'));
                }

                $this->Flash->success(__d('media', 'Folder created'));
                return $this->redirect([
                    'action' => 'index',
                    'config' => $this->config,
                    '?' => ['path' => $this->path]
                ]);
            }
            $this->set('form', $form);
        } catch (\Exception $ex) {
            $this->Flash->error($ex->getMessage());
            return $this->redirect([
                'action' => 'index',
                'config' => $this->config,
                '?' => ['path' => $this->path]
            ]);
        }

        return null;
    }


    /**
     * @return \Cake\Http\Response|null
     * @throws \Exception
     */
    public function newFile(): ?\Cake\Http\Response
    {
        if (!$this->getMediaManager()->directoryExists($this->path)) {
            $this->Flash->error(__d('media', 'Parent folder does not exist: {0}', $this->path));
            $this->redirect(['action' => 'index']);
        }

        try{
            $form = new NewFileForm();
            if ($this->getRequest()->is(['put', 'post'])) {
                if (!$form->execute($this->getRequest()->getData())) {
                    throw new MediaException(__d('media', 'Failed to create file'));
                }
                $this->Flash->success(__d('media', 'File created'));
                return $this->redirect([
                    'action' => 'index',
                    'config' => $this->config,
                    '?' => ['path' => $this->path]
                ]);
            }
            $this->set('form', $form);
        } catch (\Exception $ex) {
            $this->Flash->error($ex->getMessage());
            $this->redirect(['action' => 'index']);
        }

        return null;
    }

    protected function _getMediaFileObject()
    {
        $basePath = $this->getMediaManager()->getBasePath();
        $path = $this->path;
        $file = $this->file;

        if ($path && $file) {
            return MediaFile::fromPath($basePath . $path . $file);
        }

        return null;
    }

    /**
     * @return \Cake\Filesystem\File|null
     * @deprecated
     */
    protected function _getFileObject(): ?File
    {
        $basePath = $this->getMediaManager()->getBasePath();
        //$path = $this->request->getQuery('path') ?: '/';
        //$path = rtrim($path, '/') . '/';
        //$file = $this->request->getQuery('file');
        $path = $this->path;
        $file = $this->file;

        if ($path && $file) {
            $f = new File($basePath . $path . $file);
            return $f;
        }

        return null;
    }

    /**
     * @return Folder
     * @throws \Exception
     * @deprecated
     */
    protected function _getFolderObject(): ?Folder
    {
        $basePath = $this->getMediaManager()->getBasePath();
        //$path = $this->request->getQuery('path') ?: '/';
        //$path = rtrim($path, '/') . '/';
        $path = $this->path;

        if ($path) {
            $f = new Folder($basePath . $path);
            return $f;
        }

        return null;
    }
}
