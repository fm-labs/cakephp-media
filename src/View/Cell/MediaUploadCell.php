<?php
declare(strict_types=1);

namespace Media\View\Cell;

use Cake\Core\Configure;
use Cake\Core\Exception\MissingPluginException;
use Cake\Core\Plugin;
use Cake\View\Cell;
use Exception;
use Media\Form\MediaUploadForm;
use Media\MediaManager;
use Upload\Uploader;

/**
 * ImageSelect cell
 */
class MediaUploadCell extends Cell
{
    /**
     * @inheritDoc
     */
    protected $_validCellOptions = ['mediaConfig', 'uploadConfig'];

    /**
     * @var \Media\MediaManager|null
     */
    protected ?MediaManager $_mediaManager = null;

    /**
     * @var string Media config name
     */
    protected string $mediaConfig = 'default';
    protected array $uploadConfig = [];

    /**
     * @return \Media\MediaManager
     * @throws \Exception
     */
    public function getMediaManager(): MediaManager
    {
        if (!$this->_mediaManager) {
            $this->_mediaManager = MediaManager::get($this->mediaConfig);
        }

        return $this->_mediaManager;
    }

    /**
     * @throws Exception
     */
    public function getUploader(): Uploader
    {
//            $uploadConfig = [
//                'uploadDir' => $uploadDir,
//                'minFileSize' => 1,
//                'maxFileSize' => 2097152, // 2MB
//                'mimeTypes' => '*', //['image/*'],
//                'fileExtensions' => '*',
//                'multiple' => false, //@TODO Multiple file upload
//                'slug' => '_',
//                'hashFilename' => false,
//                'uniqueFilename' => false,
//                'overwrite' => false,
//                'saveAs' => null, // filename override
//                //'pattern' => false, // @todo Implement me
//            ];
//            $uploaderConfig = array_merge($uploaderConfig, $params['uploader']);
        $uploadConfig = $this->uploadConfig;
        $uploader = new Uploader($uploadConfig);

        return $uploader;
    }

    /**
     * Default display method.
     *
     * @param array $params The cell params
     * @return void
     */
    public function display(array $params = []): void
    {
        $params += ['config' => null, 'uploader' => []];
        //$config = $params['config'] ?? 'default';

        $path = $this->request->getQuery('path') ?: '/';
        $path = trim($path, '/') . '/';

        //$uploadUrl = ['plugin' => 'Media', 'controller' => 'Upload', 'action' => 'upload'];

        $uploadDir = $uploadForm = $upload = null;
        try {
            if (!Plugin::isLoaded('Upload')) {
                throw new MissingPluginException(['plugin' => 'Upload']);
            }

            $uploadDir = $this->getMediaManager()->getBasePath() . $path;
            $uploader = $this->getUploader();
            $uploader->setUploadDir($uploadDir);
            $this->set('uploadMultiple', $uploader->getConfig('multiple'));
            $this->set('uploadConfig', $uploader->getConfig());

            $uploadForm = new MediaUploadForm($this->mediaConfig, $uploader);
            if ($this->request->is('post')) {
                $uploadedFiles = $this->request->getUploadedFiles();
                $uploadForm->execute($uploadedFiles);
                $upload = $uploadForm->getUploadedFiles();
            }
        } catch (Exception $ex) {
            $error = __d('media', 'Can not load upload form');
            if (Configure::read('debug')) {
                $error .= $ex->getMessage();
            }

            $this->set('error', $error);
        }

        $this->set('uploadPath', $path);
        $this->set('uploadDir', $uploadDir);
        $this->set('uploadForm', $uploadForm);
        $this->set('upload', $upload);
    }
}
