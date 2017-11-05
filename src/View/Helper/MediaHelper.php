<?php

namespace Media\View\Helper;

use Cake\Log\Log;
use Cake\Utility\Inflector;
use Cake\Utility\Text;
use Cake\View\Helper;
use Cake\View\Helper\FormHelper;
use Cake\View\Helper\HtmlHelper;
use Cake\View\Helper\UrlHelper;
use Cake\View\View;
use Media\Lib\Image\ImageProcessor;

/**
 * Class MediaHelper
 * @package Media\View\Helper
 *
 * @property HtmlHelper $Html
 * @property UrlHelper $Url
 * @property FormHelper $Form
 */
class MediaHelper extends Helper
{
    public $helpers = ['Html', 'Url', 'Form'];

    /**
     * @var ImageProcessor
     */
    protected $_processor;

    public function __construct(View $View, array $config = [])
    {
        parent::__construct($View, $config);

        // Load ImageProcessor if Imagine is available
        try {
            $processor = new ImageProcessor();
            if ($processor->imagine() !== null) {
                $this->_processor = $processor;
            }
        } catch (\Exception $ex) {
            Log::warning('MediaHelper: ' . $ex->getMessage(), ['media']);
        }
    }

    public function thumbnailUrl($source, $options = [], $full = false)
    {
        $thumbUrl = $this->_generateThumbnail($source, $options);
        if (!$thumbUrl) {
            return false;
        }

        return $this->Url->build($thumbUrl, $full);
    }

    public function thumbnail($source, $options = [], $attr = [])
    {
        $thumbUrl = $this->_generateThumbnail($source, $options);
        if ($thumbUrl) {
            return $this->Html->image($thumbUrl, $attr);
        }
    }

    protected function _generateThumbnail($source, $options = [])
    {
        if (!$this->_processor) {
            debug("Media image processor not loaded");
            return false;
        }

        if (!file_exists($source) || preg_match('/\:\/\//', $source)) {
            debug("Source image not found at " . $source);
            return false;
        }

        $info = pathinfo($source);
        $filename = Text::slug($info['filename'], '_');

        $thumbBasename =  $filename . '_' . md5($source . serialize($options)) . '.' . $info['extension'];
        $thumbPath = WWW_ROOT . 'cache/' . $thumbBasename;
        $thumbUri = '/cache/' . $thumbBasename;

        // cached thumbnail
        if (file_exists($thumbPath)) {
            return $thumbUri;
        }

        // render thumbnail
        try {
            $this->_processor
                ->open($source)
                ->thumbnail($options)
                ->save($thumbPath);

            Log::info('MediaHelper: Created thumb for ' . $source . ': ' . $thumbPath, ['media']);

        } catch (\Exception $ex) {
            debug($ex->getMessage());
            Log::error('MediaHelper: Thumb generation failed:' . $ex->getMessage(), ['media']);
            return false;
        }

        return $thumbUri;
    }
}
