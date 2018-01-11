<?php
namespace Media\Model\Entity;

use Cake\ORM\Entity;
use Cake\Routing\Router;
use Media\Lib\Media\MediaManager;

class MediaFile extends Entity implements MediaFileInterface
{
    protected $_accessible = [
        'config' => true,
        'basePath' => false,
        'path' => true,
        //'originalpath' => false,
        //'realpath' => false,
        'basename' => false,
        'size' => false,
    ];

    protected $_virtual = [
        'basename',
        'url',
        //'full_url',
        //'filepath'
    ];

    protected $_hidden = [
        //'config',
        //'originalpath',
        //'filepath',
        //'realpath'
    ];


    public function getBasename()
    {
        return $this->basename;
    }

    public function getPath()
    {
        return $this->path;
    }

    /**
     * @deprecated Use getPath() instead
     */
    public function getFilePath()
    {
        return $this->_getFilepath();
    }

    /**
     * @param bool $full
     * @param @deprecated bool $encoded The $encoded param is deprecated. Use getUrlEncoded() instead()
     * @return string Url to media file
     */
    public function getUrl($full = false, $encoded = false)
    {
        //@deprecated The $encoded param is deprecated. Use getUrlEncoded() instead()
        if ($encoded == true) {
            return $this->getUrlEncoded($full);
        }

        $url = MediaManager::get($this->config)->getFileUrl($this->path);
        if ($full) {
            $url = Router::url($url, $full);
        }

        return $url;
    }

    public function getUrlEncoded($full = false) {

        $url = MediaManager::get($this->config)->getFileUrlEncoded($this->path);
        if ($full) {
            $url = Router::url($url, $full);
        }

        return $url;
    }

    public function isImage()
    {
        $basename = $this->_getBasename();

        return (preg_match('/\.(jpeg|jpg|gif|png)$/i', strtolower($basename)));
    }

    protected function _setPath($path)
    {
//        $this->_properties['originalpath'] = $path;
//
//        if (preg_match('|^media\:\/\/([\w\_]+)\/(.*)$|', $path, $matches)) {
//            $this->set('config', $matches[1]);
//
//            return $matches[2];
//        }

        return $path;
    }

    /**
     * Return file size
     */
    protected function _getSize()
    {
        if (!isset($this->_properties['size'])) {
            if (is_file($this->path)) {
                $this->_properties['size'] = @filesize($this->path);
            }
        }
        return $this->_properties['size'];
    }

    /**
     * @deprecated Use _getSize() instead
     */
    protected function _getFilesize()
    {
        return $this->_getSize();
    }

    protected function _getFilepath()
    {
        return MediaManager::get($this->config)->getBasePath() . $this->path;
    }

    protected function _getUrl()
    {
        return $this->getUrl(true);
    }

    /**
     * @deprecated Use _getUrl() instead
     */
    protected function _getFullUrl()
    {
        return $this->getUrl(true);
    }

    protected function _getBasename()
    {
        if (!isset($this->_properties['basename'])) {
            $this->_properties['basename'] = basename($this->path);
        }
        return $this->_properties['basename'];
    }

}
