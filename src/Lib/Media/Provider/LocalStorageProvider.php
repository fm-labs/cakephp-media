<?php
declare(strict_types=1);

namespace Media\Lib\Media\Provider;

use Cake\Filesystem\Folder;

/**
 * Class LocalStorageProvider
 *
 * MediaProvider for local file systems
 *
 * @package Media\Lib\Media\Provider
 */
class LocalStorageProvider extends AbstractMediaProvider
{
    protected $_defaultConfig = [
        'basePath' => null,
        'baseUrl' => null,
    ];

    /**
     * @var string|null Absolute path to local directory
     */
    protected $_basePath;

    /**
     * @var string|null Base URL
     */
    protected $_baseUrl;

    /**
     * @var string Current working path
     */
    protected $_path;

    /**
     * @var \Cake\Filesystem\Folder
     */
    protected $_Folder;

    /**
     * @throws \Exception
     */
    public function initialize()
    {
        $basePath = $this->getConfig('basePath');
        if (!$basePath) {
            throw new \InvalidArgumentException("LocalStorage: Base path not defined");
        }
        if (!is_dir($basePath)) {
            throw new \Exception("LocalStorage: Base path not found: " . $basePath);
        }
        if (!is_readable($basePath)) {
            throw new \Exception(__d('media', "LocalStorage: Root path *{0}* is not accessible", $basePath));
        }
        $this->_basePath = rtrim($basePath, '/') . '/';
        $this->_baseUrl = $this->getConfig('baseUrl');
        $this->_path = '/';
    }

    protected function _getRealPath($path = '/')
    {
        return $this->_basePath . ltrim($path, '/');
    }

    protected function _connect()
    {
        if (!$this->_Folder) {
            $this->_Folder = new Folder($this->_basePath, false);
        }
    }

    /**
     * Read contents of directory path
     * @param $path string Path to directory
     * @return array List of files and directories
     * @throws \Exception
     */
    public function read(string $path): array
    {
        $this->_connect();

        $path = $this->_getRealPath($path);
        if (!is_dir($path)) {
            throw new \Exception("Directory path not found: " . $path);
        }
        if (!$this->_Folder->cd($path)) {
            throw new \Exception("Failed to open directory path");
        }

        return $this->_Folder->read();
    }

    /**
     * @inheritDoc
     */
    public function createDirectory(string $path): bool
    {
        // TODO: Implement createDirectory() method.
        return false;
    }

    /**
     * @inheritDoc
     */
    public function directoryExists(string $path): bool
    {
        // TODO: Implement createDirectory() method.
        return false;
    }

    /**
     * @inheritDoc
     */
    public function createFile(string $path): bool
    {
        // TODO: Implement createFile() method.
        return false;
    }

    /**
     * @inheritDoc
     */
    public function fileExists(string $path): bool
    {
        // TODO: Implement fileExists() method.
        return false;
    }

    /**
     * @inheritDoc
     */
    public function writeFile(string $path, string $contents): bool
    {
        // TODO: Implement writeFile() method.
        return false;
    }

    /**
     * @inheritDoc
     */
    public function deleteDirectory(string $path): bool
    {
        // TODO: Implement deleteDirectory() method.
        return false;
    }

    /**
     * @inheritDoc
     */
    public function deleteFile(string $path): bool
    {
        // TODO: Implement deleteFile() method.
        return false;
    }

    /**
     * @inheritDoc
     */
    public function move(string $source, string $destination): bool
    {
        // TODO: Implement move() method.
        return false;
    }
}
