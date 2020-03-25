<?php
namespace Media\Lib\Media;

use Cake\Core\App;
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
     */
    protected $_path;

    /**
     * @param $config
     * @return MediaProviderInterface
     * @throws \Exception
     */
    public static function getProvider($config)
    {

        if (is_string($config) && in_array($config, self::configured())) {
            $config = self::getConfig($config);
        } elseif (!is_array($config)) {
            throw new \InvalidArgumentException("Invalid configuration '" . (string)$config . "'");
        }

        $config = array_merge([
            'label' => 'Default',
            'className' => null,
            'public' => false,
            'baseUrl' => null,
            'basePath' => null,
        ], $config);

        if (isset($config['provider'])) {
            $config['className'] = $config['provider'];
            unset($config['provider']);
        }

        // @TODO Remove
        if (isset($config['url'])) {
            triggerWarning(sprintf("The parameter 'url' is deprecated. Use 'baseUrl' instead."));
            $config['baseUrl'] = $config['url'];
            unset($config['url']);
        }
        if (isset($config['path'])) {
            triggerWarning(sprintf("The parameter 'path' is deprecated. Use 'basePath' instead."));
            $config['basePath'] = $config['path'];
            unset($config['path']);
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
        if (!($providerObj instanceof MediaProviderInterface)) {
            throw new \Exception("Provider is not a valid MediaProviderInterface");
        }

        return $providerObj;
    }

    public static function get($config)
    {
        $provider = self::getProvider($config);
        return new self($provider);
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
     * @todo Mark as deprecated
     */
    public function setPath($path)
    {
        $this->_path = $this->_normalizePath($path);
    }

    /**
     * @todo Mark as deprecated
     */
    public function getPath()
    {
        return $this->_path;
    }

    /**
     * @todo Mark as deprecated
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
        list(, $files) = $this->read($path);
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
     * @todo Mark as deprecated. This should not be used anymore. And will be removed.
     */
    public function getBasePath()
    {
        return realpath($this->_provider->getConfig('basePath')) . DS;
    }

    /**
     * @todo Mark as deprecated This should not be used anymore. And will be removed.
     */
    public function getBaseUrl()
    {
        return rtrim($this->_provider->getConfig('baseUrl'), '/');
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
    throw new MediaException(__d('media',"Media provider with name {0} is already mounted", $name));
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
    throw new MediaException(__d('media',"Media provider with name {0} has not been registered", $name));
    }
    return $this->_mounts[$name];
    }
 */
}
