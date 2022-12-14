<?php
declare(strict_types=1);

namespace Media\Model\Entity;

use Cake\Filesystem\File;
use Cake\ORM\Entity;
use Cake\Routing\Router;
use Media\Lib\Media\MediaException;
use Media\Lib\Media\MediaManager;

/**
 * Class MediaFile
 *
 * @package Media\Model\Entity
 * @property string $config
 * @property string $basePath
 * @property string $path
 * @property string $basename
 * @property int $size
 */
class MediaFile extends Entity implements MediaFileInterface
{
    /**
     * @var array
     */
    protected $_accessible = [
        'config' => true,
        'path' => true,
        'basePath' => false,
        'basename' => false,
        'size' => false,
    ];

    /**
     * @var string[]
     */
    protected $_virtual = [
        'basename',
        'url',
        'filepath',
    ];

    /**
     * @var array
     */
    protected $_hidden = [
        'filepath',
    ];

    /**
     * @return bool
     */
    public function isImage(): bool
    {
        $basename = $this->_getBasename();

        return preg_match('/\.(jpeg|jpg|gif|png)$/i', strtolower($basename)) ? true : false;
    }

    /**
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    public function getSize(): ?int
    {
        return $this->size;
    }

    /**
     * @return string
     */
    public function getBasename(): string
    {
        return $this->basename;
    }

    /**
     * @return string
     */
    public function getFilePath(): string
    {
        return $this->_getFilepath();
    }

    /**
     * @param bool $full
     * @param bool $encoded The $encoded param is deprecated. Use getUrlEncoded() instead()
     * @return string Url to media file
     */
    public function getUrl($full = false, $encoded = false): string
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

    /**
     * @param false $full Full url flag
     * @return string
     */
    public function getUrlEncoded($full = false): string
    {
        $url = MediaManager::get($this->config)->buildFileUrlEncoded($this->path);

        return Router::url($url, $full);
    }

//    /**
//     * @param string $path
//     * @return string
//     */
//    protected function _setPath(string $path): string
//    {
////        $this->_properties['originalpath'] = $path;
////
////        if (preg_match('|^media\:\/\/([\w\_]+)\/(.*)$|', $path, $matches)) {
////            $this->set('config', $matches[1]);
////
////            return $matches[2];
////        }
//
//        return $path;
//    }

    /**
     * Return file size.
     *
     * @return int
     */
    protected function _getSize(): int
    {
        if (!isset($this->_fields['size'])) {
            if (is_file($this->filepath)) {
                $this->_fields['size'] = @filesize($this->filepath);
            }
        }

        return $this->_fields['size'] ?? 0;
    }

    /**
     * Absolute path to file including filename
     *
     * @return string
     */
    protected function _getFilepath(): string
    {
        if (!isset($this->_fields['filepath'])) {
            $this->_fields['filepath'] = MediaManager::get($this->config)->getBasePath() . $this->path;
        }

        return $this->_fields['filepath'];
    }

    /**
     * @return string
     */
    protected function _getUrl(): string
    {
        return $this->getUrl(true);
    }

    /**
     * @return string
     */
    protected function _getBasename(): string
    {
        if (!isset($this->_fields['basename'])) {
            $this->_fields['basename'] = basename($this->path);
        }

        return $this->_fields['basename'];
    }

    /**
     * @return string
     * @deprecated Use _getUrl() instead
     */
    protected function _getFullUrl(): string
    {
        deprecationWarning('MediaFile::_getFullUrl() is deprecated. Use _getUrl() instead.');

        return $this->getUrl(true);
    }

    /**
     * @return int
     * @deprecated Use _getSize() instead
     */
    protected function _getFilesize(): int
    {
        deprecationWarning('MediaFile::_getFullUrl() is deprecated. Use _getUrl() instead.');

        return $this->_getSize();
    }

    /**
     * @param \Cake\Filesystem\File $file File object
     * @return self
     * @throws \Media\Lib\Media\MediaException
     * @deprecated Use fromPath() instead
     */
    public static function fromFile(File $file): self
    {
        return static::fromPath($file->path);
    }

    /**
     * @param string $path File path
     * @param string $configName Media config name
     * @return self
     * @throws \Media\Lib\Media\MediaException
     */
    public static function fromPath(string $path, string $configName = 'default'): self
    {
        //$info = pathinfo($path);
        $basePath = MediaManager::get($configName)->getBasePath();

        if (substr($path, 0, strlen($basePath)) != $basePath) {
            throw new MediaException("The path '$path' is not in base path '$basePath'");
        }
        $_path = substr($path, strlen($basePath));

        $mf = new self();
        $mf->config = $configName;
        $mf->path = $_path;
        //$mf->basename = $info['basename'];

        return $mf;
    }
}
