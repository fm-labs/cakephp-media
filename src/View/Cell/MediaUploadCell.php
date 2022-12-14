<?php
declare(strict_types=1);

namespace Media\View\Cell;

use Cake\Core\Configure;
use Cake\Core\Exception\MissingPluginException;
use Cake\Core\Plugin;
use Cake\View\Cell;
use Media\Form\MediaUploadForm;
use Media\Lib\Media\MediaManager;
use Upload\Uploader;

/**
 * ImageSelect cell
 */
class MediaUploadCell extends Cell
{
    /**
     * List of valid options that can be passed into this
     * cell's constructor.
     *
     * @var array
     */
    protected $_validCellOptions = [];

    /**
     * @var \Media\Lib\Media\MediaManager
     */
    protected $_mediaManager;

    /**
     * @var string Media config name
     */
    protected $_mediaConfig = 'default';

    /**
     * @return \Media\Lib\Media\MediaManager
     */
    public function getMediaManager()
    {
        if (!$this->_mediaManager) {
            $this->_mediaManager = MediaManager::get($this->_mediaConfig);
        }

        return $this->_mediaManager;
    }

    /**
     * Default display method.
     *
     * @param array $params The cell params
     * @return void
     */
    public function display($params = [])
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
            $uploaderConfig = [
                'uploadDir' => $uploadDir,
                'minFileSize' => 1,
                'maxFileSize' => 2097152, // 2MB
                'mimeTypes' => '*', //['image/*'],
                'fileExtensions' => '*',
                'multiple' => false, //@TODO Multiple file upload
                'slug' => '_',
                'hashFilename' => false,
                'uniqueFilename' => false,
                'overwrite' => false,
                'saveAs' => null, // filename override
                //'pattern' => false, // @todo Implement me
            ];
            $uploaderConfig = array_merge($uploaderConfig, $params['uploader']);
            $uploader = new Uploader($uploaderConfig);
            $uploader->setUploadDir($uploadDir);
            $this->set('uploadMultiple', $uploader->getConfig('multiple'));

            $uploadForm = new MediaUploadForm($this->_mediaConfig, $uploader);
            if ($this->request->is('post')) {
                //debug($this->request->getData());
                $uploadForm->execute($this->request->getData());
                $upload = $uploadForm->getUploadedFiles();
            }
        } catch (\Exception $ex) {
            $error = __('Can not load upload form');
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
