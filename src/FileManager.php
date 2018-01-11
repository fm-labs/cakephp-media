<?php

namespace Media;

use Cake\Core\App;
use Cake\Core\StaticConfigTrait;
use Media\Lib\FileManager\Provider\MediaProviderInterface;

/**
 * Class FileManager
 */
class FileManager
{
    use StaticConfigTrait;

    protected static $_dsnClassMap = [];

    /**
     * @var MediaProviderInterface
     */
    protected $_provider;

    /**
     * @param $config
     * @return FileManager
     * @throws \Exception
     */
    static public function createInstance($config)
    {
        if (is_string($config) && in_array($config, self::configured())) {
            $config = self::config($config);
        }
        elseif (!is_array($config)) {
            throw new \InvalidArgumentException("Invalid configuration");
        }

        $config = array_merge([
            'label' => 'Default',
            'className' => null,
            //'public' => false,
            //'url' => false,
        ], $config);

        $provider = $config['className'];
        if (!$provider) {
            throw new \Exception("Provider not configured");
        }

        $className = App::className($provider, 'Lib/FileManager/Provider', 'Provider');
        if (!$className) {
            throw new \Exception("Provider class not found");
        }

        $providerObj = new $className($config);
        if ($providerObj instanceof MediaProviderInterface) {
            return new self($providerObj);
        }

        throw new \Exception("Provider is not a valid MediaProviderInterface");
    }

    /**
     * @param MediaProviderInterface $provider
     */
    public function __construct(MediaProviderInterface $provider)
    {
        $this->_provider = $provider;
    }

    public function __destruct()
    {
        $this->_provider->disconnect();
    }

    /**
     * @return array
     */
    public function read($path)
    {
        $this->_provider->connect();
        return $this->_provider->read($path);
    }
}