<?php
namespace Media\Model\Entity;

use Cake\ORM\Entity;
use Media\Lib\Media\MediaManager;

class MediaFile extends Entity
{
    protected $_accessible = [
        'config' => true,
        'path' => true,
        'realpath' => false,
        'basename' => false,
        'filesize' => false,
    ];

    protected function _setPath($path)
    {
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
        return MediaManager::get($this->config)->getPath() . $this->path;
    }

    protected function _getUrl()
    {
        return MediaManager::get($this->config)->getFileUrl($this->path);
    }

    protected function _getBasename()
    {
        return basename($this->path);
    }

    public function __toString()
    {
        return $this->path;
    }
}