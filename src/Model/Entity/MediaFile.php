<?php
namespace Media\Model\Entity;

use Cake\ORM\Entity;
use Cake\Routing\Router;
use Media\Lib\Media\MediaManager;

class MediaFile extends Entity
{
    protected $_accessible = [
        'config' => true,
        'path' => true,
        'originalpath' => false,
        'realpath' => false,
        'basename' => false,
        'filesize' => false,
    ];

    protected $_virtual = [
        'basename',
        'url', 'full_url',
        'filepath'
    ];

    protected $_hidden = [
        'config',
        'originalpath',
        'filepath'
    ];

    protected function _setPath($path)
    {
        $this->_properties['originalpath'] = $path;

        if (preg_match('|^media\:\/\/([\w\_]+)\/(.*)$|', $path, $matches)) {
            $this->set('config', $matches[1]);
            return $matches[2];
        }

        return $path;
    }

    protected function _getFilesize()
    {
        if (is_file($this->path)) {
            return @filesize($this->path);
        }
    }

    /**
     * @deprecated
     */
    protected function _getRealpath() {
        return $this->_getFilepath();
    }

    protected function _getFilepath() {
        return MediaManager::get($this->config)->getBasePath() . $this->path;
    }

    protected function _getUrl()
    {
        return $this->getUrl();
    }

    protected function _getFullUrl()
    {
        return $this->getUrl(true);
    }

    protected function _getBasename()
    {
        return basename($this->path);
    }

    public function getUrl($full = false)
    {
        $url = MediaManager::get($this->config)->getFileUrl($this->path);
        if ($full) {
            $url = Router::url($url, $full);
        }
        return $url;
    }

    public function __toString()
    {
        return $this->path;
    }
}