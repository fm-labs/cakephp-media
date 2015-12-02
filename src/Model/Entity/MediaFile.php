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

    protected function _getFilesize()
    {
        if (is_file($this->path)) {
            return @filesize($this->path);
        }
    }

    protected function _getRealPath() {
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
}