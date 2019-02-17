<?php
namespace Media\View\Cell;

use Cake\Core\Exception\MissingPluginException;
use Cake\Core\Plugin;
use Cake\ORM\TableRegistry;
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
     * @var MediaManager
     */
    protected $_mediaManager;

    /**
     * @return MediaManager
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

        $path = ($this->request->query('path')) ?: '/';
        $path = trim($path, '/') . '/';

        $uploadDir = $this->getMediaManager()->getBasePath() . $path;
        $uploadForm = $upload = null;

        try {
            if (!Plugin::loaded('Upload')) {
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
            $this->set('uploadMultiple', $uploader->config('multiple'));

            $uploadForm = new MediaUploadForm('default', $uploader);
            if ($this->request->is('post')) {
                //debug($this->request->data);
                $upload = $uploadForm->execute($this->request->data);
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
