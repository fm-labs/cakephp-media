<?php

namespace Media\View\Helper;

use Cake\Core\Configure;
use Cake\Filesystem\File;
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
class MediaBrowserHelper extends Helper
{
    public $helpers = ['Html', 'Url', 'Form'];

    /**
     * @var \Media\Lib\Media\MediaManager
     */
    protected $_manager;

    public function setMediaManager($manager = null)
    {
        $this->_manager = $manager;

        return $this;
    }

    public function fileIcon($path, $file)
    {
        $icon = 'file-o';

        $f = $this->_getFile($path, $file);
        $map = [
            'pdf' => 'file-pdf-o',
            'jpg' => 'file-image-o',
            'jpeg' => 'file-image-o',
            'gif' => 'file-image-o',
            'png' => 'file-image-o',
            'mp3' => 'file-audio-o',
            'wav' => 'file-audio-o',
            'ogg' => 'file-audio-o',
            'txt' => 'file-text-o',
            'json' => 'file-text-o',
            'xml' => 'file-text-o',
            'html' => 'file-code-o',
            'php' => 'file-code-o',
            'mp4' => 'file-video-o',
            'zip' => 'file-archive-o',
            'tar' => 'file-archive-o',
            'tar.gz' => 'file-archive-o',
            '7z' => 'file-archive-o',
            'xls' => 'file-excel-o',
            'xlsx' => 'file-excel-o',
            'doc' => 'file-word-o',
            'docx' => 'file-word-o',
            'odt' => 'file-word-o',
        ];

        $ext = strtolower($f->ext());
        if (array_key_exists($ext, $map)) {
            $icon = $map[$ext];
        }

        return '<i class="fa fa-' . $icon . '"></i>';
    }

    public function getSourcePath($path, $file)
    {
        $f = $this->_getFile();

        return $f->path;
    }

    protected function _getFile($path, $file)
    {

        $basePath = $this->_manager->getBasePath();
        $path = rtrim($path, '/') . '/';

        $f = new File($basePath . $path . $file);

        return $f;
    }
}
