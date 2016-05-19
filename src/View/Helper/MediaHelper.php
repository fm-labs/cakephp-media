<?php

namespace Media\View\Helper;

use Cake\Core\Plugin;
use Cake\View\Helper;
use Cake\View\View;
use Media\Lib\Image\ImageProcessor;

class MediaHelper extends Helper
{
    public $helpers = ['Html'];

    /**
     * @var ImageProcessor
     */
    protected $_processor;

    public function __construct(View $View, array $config = [])
    {
        parent::__construct($View, $config);

        // Load ImageProcessor if Imagine is available
        $processor = new ImageProcessor();
        if ($processor->imagine() !== null) {
            $this->_processor = $processor;
        }
    }

    public function thumbnail($source, $options = [], $attr = [])
    {
        $source = $this->_generateThumbnail($source, $options);
        return $this->Html->image($source, $attr);
    }

    protected function _generateThumbnail($source, $options = []) {

        if (!$this->_processor) {
            return $source;
        }

        if (!file_exists($source) || preg_match('/\:\/\//', $source)) {
            return $source;
        }

        $info = pathinfo($source);

        $thumbBasename = $info['filename'] . '_' . md5(serialize($options)) . '.' . $info['extension'];
        $thumbPath = WWW_ROOT . 'cache/' . $thumbBasename;
        $thumbUri = '/cache/' . $thumbBasename;

        if (file_exists($thumbPath)) {
            return $thumbUri;
        }

        try {

            $this->_processor
                ->open($source)
                ->thumbnail($options)
                ->save($thumbPath);

            return $thumbUri;

        } catch (\Exception $ex) {
            debug($ex->getMessage());
        }

        return $source;


    }
}
