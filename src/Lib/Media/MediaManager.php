<?php
namespace Media\Lib\Media;

use Cake\Core\App;
use Cake\Core\Configure;
use Cake\Core\StaticConfigTrait;
use Media\Lib\Media\Provider\MediaProviderInterface;

class MediaManager
{
    use StaticConfigTrait;

    protected static $_dsnClassMap = [];

    /**
     * @var MediaProviderInterface
     */
    protected $_provider;

    /**
     * @var string Current working dir
     * @deprecated
     */
    protected $_path;

    /**
     * @param $config
     * @return $this
     * @throws \Exception
     */
    static public function getProvider($config)
    {
        if (empty(self::configured())) {
            self::config(Configure::read('Media'));
        }

        if (is_string($config) && in_array($config, self::configured())) {
            $config = self::config($config);
        }
        elseif (!is_array($config)) {
            throw new \InvalidArgumentException("Invalid configuration '" . (string) $config . "'");
        }

        $config = array_merge([
            'label' => 'Default',
            'className' => null,
            'public' => false,
            //'baseUrl' => null,
            //'basePath' => null,
        ], $config);

        if (isset($config['provider'])) {
            $config['className'] = $config['provider'];
            unset($config['provider']);
        }

        //debug($config);

        $provider = $config['className'];
        if (!$provider) {
            throw new \Exception("Provider not configured");
        }

        $className = App::className($provider, 'Lib/Media/Provider', 'Provider');
        if (!$className) {
            throw new \Exception("Provider class not found");
        }

        $providerObj = new $className($config);
        if ($providerObj instanceof MediaProviderInterface) {
            return new self($providerObj);
        }

        throw new \Exception("Provider is not a valid MediaProviderInterface");
    }


    public static function get($configName)
    {
        /*
        $configKey = 'Media.' . $configName;
        $config = Configure::read($configKey);
        if (!$config) {
            throw new \Exception(__('Media config {0} does not exist', $configName));
        }
        $config = array_merge([
            'label' => 'Unlabeled',
            'name' => null,
            'provider' => null,
            'public' => false,
            'url' => false,
        ], $config);

        $provider = $config['provider'];
        $className = App::className($provider, 'Lib/Media/Provider', 'Provider');
        $providerIns = new $className($config);

        return new self($providerIns);
        */
        return self::getProvider($configName);
    }

    public function __construct(MediaProviderInterface $provider)
    {
        //$this->mount('default', new LocalStorageProvider(MEDIA));
        //$this->mount('dropbox', new DropboxProvider());
        $this->_provider = $provider;
        //$this->_provider->connect();
        //$this->open('/');
    }

    /** PROVIDER INTERFACE **/

    public function read($path)
    {
        $this->setPath($path);
        return $this->_provider->read($this->_path);
    }



    /** MEDIA BROWSING **/

    /**
     * @deprecated
     */
    public function setPath($path)
    {
        $this->_path = $this->_normalizePath($path);
    }

    /**
     * @deprecated
     */
    public function getPath()
    {
        return $this->_path;
    }

    /**
     * @deprecated
     */
    public function getParentPath()
    {
        $path = $this->_path;
        $path = trim($path, '/');
        $parts = explode('/', $path);
        if (count($parts) <= 1) {
            return '/';
        }

        array_pop($parts);

        return join('/', $parts);
    }

//    public function open($path)
//    {
//        $this->setPath($path);
//        return $this;
//    }
    
    public function listFiles($path)
    {
        $path = $this->_normalizePath($path);
        list($files, ) = $this->read($path);
        array_walk($files, function (&$file, $idx) use ($path) {
            $file = $path . $file;
        });

        return $files;
    }

    public function listFileUrls($path)
    {
        $urls = [];
        $files = $this->listFiles($path);
        array_walk($files, function ($val, $idx) use (&$urls) {
            $urls[$val] = $this->buildFileUrl($val);
        });

        return $urls;
    }

    public function listFilesRecursive($path, $fullPath = false)
    {
        $list = [];
        $basePath = ($fullPath) ? $this->getBasePath() : '';

        $path = $this->_normalizePath($path);
        list($files, $dirs) = $this->read($path);
        array_walk($files, function ($file) use ($basePath, $path) {
            $list[] = $basePath . $path . $file;
        });

        array_walk($dirs, function ($dir) use (&$list, $path, $fullPath) {
            $files = $this->listFilesRecursive($path . $dir, $fullPath);
            foreach ($files as $file) {
                $list[] = $file;
            }
        });

        return $list;
    }

    public function listFolders($path)
    {
        list($dirs, ) = $this->read($path);

        return $dirs;
    }

    public function listFoldersRecursive($path, $depth = -1)
    {
        $path = $this->_normalizePath($path);
        list($dirs, ) = $this->read($path);

        $list = [];
        array_walk($dirs, function (&$dir, $idx) use (&$list, &$path, &$depth) {
            //$_dir = $this->_normalizePath($path . $dir);
            $_dir = $path . $dir;
            $list[] = $_dir;

            if ($depth > -1 && $depth == 0) {
                return;
            }

            foreach ($this->listFoldersRecursive($_dir . '/', $depth - 1) as $dir) {
                $list[] = $dir;
            }
        });

        return $list;
    }

    public function deleteFile($path)
    {
        //$this->_provider->delete($path);
    }


    /**
     * Normalize Path
     *
     * Strip leading path separator
     * Append trailing path separator if not root path
     *
     * @param $path
     * @return string
     */
    protected function _normalizePath($path)
    {
        $path = trim(trim($path), '/') . '/';
        $path = preg_replace('|([\/])?\.\.\/|', '/', $path); // clean path patterns like '/../../'
        $path = preg_replace('|([\/])?\.\/|', '/', $path); // clean path patterns like '/././'
        $path = preg_replace('|[\/]+|', '/', $path); // clean path patterns like '/////path///to//dir///'
        $path = ltrim($path, '/');
        $path = ($path == '/') ? '' : $path;

        return $path;
    }


    public function buildFileUrl($filePath)
    {
        //@TODO sanitize file path
        //if (strpos($filePath, '..') !== false) {
        //    return;
        //}
        $filePath = trim($filePath, '/');

        return $this->getBaseUrl() . '/' . $filePath;
    }

    public function buildFileUrlEncoded($filePath)
    {
        $url = urlencode($filePath);
        $url = preg_replace('/\%2F/', '/', $url);
        $url = ltrim($url, '/');

        return $this->getBaseUrl() . '/' . $url;
    }

    /**
     * @deprecated Use buildFileUrl() instead
     */
    public function getFileUrl($filePath)
    {
        return $this->buildFileUrl($filePath);
    }

    /**
     * @deprecated Use buildFileUrlEncoded() instead
     */
    public function getFileUrlEncoded($filePath)
    {
        return $this->buildFileUrlEncoded($filePath);
    }

    /**
     * @deprecated This should not be used anymore. And will be removed.
     */
    public function getBasePath()
    {
        return realpath($this->_provider->config('basePath')) . DS;
    }

    /**
     * @deprecated This should not be used anymore. And will be removed.
     */
    public function getBaseUrl()
    {
        return rtrim($this->_provider->config('baseUrl'), '/');
    }

    public function getSelectListRecursive($path = '/')
    {
        $files = $this->listFilesRecursive($path, false);
        $list = [];
        array_walk($files, function ($val, $idx) use (&$list) {
            $list[$val] = $this->buildFileUrl($val);
        });

        return $list;
    }

    public function getSelectFolderListRecursive($path = '/')
    {
        $files = $this->listFoldersRecursive($path);
        $list = [];
        array_walk($files, function ($val, $idx) use (&$list) {
            $list[$val] = $val;
        });

        return $list;
    }

    public function getSelectListRecursiveGrouped($path = '/')
    {
        $folders = $this->listFoldersRecursive($path);
        $list = [];

        foreach ($folders as $folder) {
            $files = $this->listFiles($folder);

            if (empty($files)) {
                continue;
            }

            $list[$folder] = [];
            array_walk($files, function ($val, $idx) use (&$list, $folder) {
                $list[$folder][$val] = $this->buildFileUrl($val);
            });
        }

        return $list;
    }

    /**
     * Mount a media provider with a name
     *
     * @param $name
     * @param MediaProviderInterface $provider
     * @throws MediaException
     *
    public function mount($name, MediaProviderInterface $provider)
    {
    if (isset($this->_mounts[$name])) {
    throw new MediaException(__("Media provider with name {0} is already mounted", $name));
    }
    $provider->connect();
    $this->_mounts[$name] = $provider;
    }
     */

    /**
     * Un-mount a media provider by name
     *
     * @param $name
    public function unmount($name)
    {
    $provider = $this->get($name);
    $provider->disconnect();
    unset($this->_mounts[$name]);
    }
     */

    /**
     * Returns the instance of a MediaProviderInterface
     *
     * @param $name
     * @return MediaProviderInterface
     * @throws MediaException
    public function get($name)
    {
    if (!isset($this->_mounts[$name])) {
    throw new MediaException(__("Media provider with name {0} has not been registered", $name));
    }
    return $this->_mounts[$name];
    }
     */
}
