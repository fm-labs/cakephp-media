<?php
declare(strict_types=1);

namespace Media\View\Cell;

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
     * @return \Media\Lib\Media\MediaManager
     */
    public function getMediaManager()
    {
        if (!$this->_mediaManager) {
            $this->_mediaManager = MediaManager::get('default');
        }

        return $this->_mediaManager;
    }

    /**
     * Default display method.
     *
     * @return void
     */
    public function display($params = [])
    {
        $params += ['config' => null, 'uploader' => null];

        $path = $this->request->getQuery('path') ?: '/';
        $path = trim($path, '/') . '/';

        $uploadDir = $this->getMediaManager()->getBasePath() . $path;
        $uploadForm = $upload = null;

        try {
            if (!Plugin::isLoaded('Upload')) {
                throw new MissingPluginException(['plugin' => 'Upload']);
            }

            $uploader = new Uploader([
                'uploadDir' => $uploadDir,
                'minFileSize' => 1,
                'maxFileSize' => 2097152, // 2MB
                'mimeTypes' => '*', //['image/*'],
                'fileExtensions' => '*',
                'multiple' => false, //@TODO Multiple file upload
                'slug' => "_",
                'hashFilename' => false,
                'uniqueFilename' => false,
                'overwrite' => false,
                'saveAs' => null, // filename override
                //'pattern' => false, // @todo Implement me
            ]);
            $uploader->setUploadDir($uploadDir);
            $this->set('uploadMultiple', $uploader->getConfig('multiple'));

            $uploadForm = new MediaUploadForm('default', $uploader);
            if ($this->request->is('post')) {
                //debug($this->request->getData());
                $upload = $uploadForm->execute($this->request->getData());
            }
        } catch (\Exception $ex) {
            $error = $ex->getMessage();
            $this->set('error', $ex->getMessage());
        }

        $this->set('uploadPath', $path);
        $this->set('uploadDir', $uploadDir);
        $this->set('uploadForm', $uploadForm);
        $this->set('upload', $upload);
    }
}
