<?php
declare(strict_types=1);

namespace Media\View\Helper;

use Cake\Filesystem\File;
use Cake\View\Helper;

/**
 * Class MediaHelper
 * @package Media\View\Helper
 *
 * @property \Cake\View\Helper\HtmlHelper $Html
 * @property \Cake\View\Helper\UrlHelper $Url
 * @property \Cake\View\Helper\FormHelper $Form
 */
class MediaBrowserHelper extends Helper
{
    public $helpers = ['Html', 'Url', 'Form'];

    /**
     * @var \Media\MediaManager
     */
    protected $_manager;

    public function setMediaManager($manager = null)
    {
        $this->_manager = $manager;

        return $this;
    }

    public function fileIcon($path, $file)
    {
        deprecationWarning("MediaBrowserHelper::fileIcon() is deprecated. Use FileIconHelper::fromPath");
        $f = $this->_getFile($path, $file);
        if (!$f) {
            debug("MediaBrowser::fileIcon: Invalid path: $path");
            return "";
        }
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

        $icon = 'file-o';
        if ($f->path) {
            $ext = strtolower($f->ext());
            if (array_key_exists($ext, $map)) {
                $icon = $map[$ext];
            }
        }

        return '<i class="fa fa-' . $icon . '"></i>';
    }

    public function getSourcePath($path, $file)
    {
        $f = $this->_getFile($path, $file);

        return $f->path;
    }

    protected function _getFile($path, $file)
    {
        $basePath = $this->_manager->getBasePath();
        $path = trim($path, '/') . '/';

        return new File($basePath . $path . $file);
    }
}
