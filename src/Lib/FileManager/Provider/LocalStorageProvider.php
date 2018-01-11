<?php

namespace Media\Lib\FileManager\Provider;

use Cake\Core\InstanceConfigTrait;
use Cake\Filesystem\Folder;

class LocalStorageProvider implements MediaProviderInterface
{
    use InstanceConfigTrait;

    /**
     * @var array
     */
    protected $_defaultConfig = [
        'path' => WWW_ROOT
    ];

    /**
     * @var Folder
     */
    protected $Folder;

    public function __construct(array $config = [])
    {
        $this->config($config);
    }

    protected function buildPath($path = '/')
    {
        return $this->config('path') . $path;
    }

    /**
     * Connect
     */
    public function connect()
    {
        $basePath = $this->buildPath('/');
        if (!is_dir($basePath)) {
            throw new \Exception("LocalStorage: Base path not found: " . $basePath);
        }
        $this->Folder = new Folder($basePath, false);
    }

    /**
     * Disconnect
     */
    public function disconnect()
    {
        $this->Folder = null;
    }

    /**
     * Read contents of directory path
     * @param $path string Path to directory
     * @return array List of files and directories
     */
    public function read($path)
    {
        $this->Folder->cd($this->buildPath($path));
        return $this->Folder->read();
    }
}
