<?php

class LocalStorage {

    use \Cake\Core\InstanceConfigTrait;

    protected $_defaultConfig = [
        'basePath' => null
    ];

    public function __construct($config = [])
    {
        $this->config($config);

        if (!$this->config('basePath') /* || !is_readable($this->config('basePath')) */) {
            throw new \RuntimeException('LocalStorage: basePath not set');
        }
    }

    public function listFiles()
    {
        return [];
    }

    public function listDirectories()
    {
        return [];
    }

    public function read($src)
    {
        return "";
    }

    public function write($target)
    {
        return 0;
    }

}