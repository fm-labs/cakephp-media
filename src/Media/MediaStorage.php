<?php

class MediaStorage
{
    use \Cake\Core\StaticConfigTrait;

    protected $_adapter;

    public static function get($key)
    {
        $config = self::config($key);
        if (!$config) {
            throw new \RuntimeException('MediaStorage adapter ' . $key .  ' not defined');
        }

        $adapter = new LocalStorage($config);

        return new self($adapter);
    }

    public function __construct(LocalStorage $adapter)
    {
        $this->_adapter = $adapter;
    }

    /**
     * @return LocalStorage
     */
    public function adapter()
    {
        return $this->_adapter;
    }

    public function listFiles()
    {
        return $this->adapter()->listFiles();
    }

    public function listFolders()
    {
        return $this->adapter()->listFolders();
    }

    public function read($source)
    {
        return $this->adapter()->read($source);
    }

    public function write($target, $data)
    {
        return $this->adapter()->write($target, $data);
    }
}
