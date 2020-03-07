<?php

namespace Media\View\Helper;

use Cake\Core\Configure;
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

    public function initialize(array $config)
    {
        $this->Form->templater()->load('Media.form_templates');
        $this->Form->addWidget('media_picker', ['Media\View\Widget\MediaPickerWidget', '_view', 'button', 'select']);

        // Load ImageProcessor if Imagine is available
        try {
            $processor = new ImageProcessor();
            if ($processor->imagine() !== null) {
                $this->_processor = $processor;
            }
        } catch (\Exception $ex) {
            $this->_error($ex->getMessage());
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
        } elseif (Configure::read('debug')) {
            return "[x]";
        }

        return null;
    }

    protected function _generateThumbnail($source, $options = [])
    {
        if (!$this->_processor) {
            $this->_error("generateThumbnail: Image processor not loaded");

            return false;
        }

        if (!file_exists($source) || preg_match('/\:\/\//', $source)) {
            $this->_error("generateThumbnail: Source image not found at " . $source);

            return false;
        }

        $info = pathinfo($source);

        if (!in_array($info['extension'], ['jpeg', 'jpg', 'png'])) {
            $this->_error("generateThumbnail: Source file is not an image");

            return false;
        }

        $options = array_merge(['height' => 100, 'width' => 100], $options);
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

            $this->_log('generateThumbnail: CREATED: ' . $thumbPath . ' from: ' . $source, 'info');
        } catch (\Exception $ex) {
            $this->_error('generateThumbnail: FAILED: ' . $ex->getMessage());

            return false;
        }

        return $thumbUri;
    }

    protected function _log($msg, $level = 'debug')
    {
        Log::write($level, sprintf('MediaHelper: %s', $msg), ['media']);
    }

    protected function _error($msg, $log = false)
    {
        //debug($msg);
        if ($log) {
            $this->_log($msg, 'error');
        }
    }
}
