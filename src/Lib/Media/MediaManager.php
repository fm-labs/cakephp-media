<?php
namespace Media\Lib\Media;

use Cake\Core\App;
use Cake\Core\Configure;
use Media\Lib\Media\Provider\MediaProviderInterface;

class MediaManager
{
    //protected $_mounts = [];

    /**
     * @var Provider\MediaProviderInterface
     */
    protected $_provider;

    protected $_path;

    public static function get($configName)
    {
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
    }

    public function __construct(MediaProviderInterface $provider)
    {
        //$this->mount('default', new LocalStorageProvider(MEDIA));
        //$this->mount('dropbox', new DropboxProvider());
        $this->_provider = $provider;
        $this->_provider->connect();
        $this->open('/');
    }

    public function config($key)
    {
        return $this->_provider->config($key);
    }

    public function open($path)
    {
        $this->setPath($path);
        return $this;
    }

    public function listFiles()
    {
        return $this->_provider->listFiles($this->_path);
    }

    public function listFileUrls()
    {
        $files = $this->listFiles();
        $list = [];
        array_walk($files, function ($val, $idx) use (&$list) {
            $list[$val] = $this->getFileUrl($val);
        });
        return $list;
    }

    public function listFilesRecursive($fullPath = false)
    {
        return $this->_provider->listFilesRecursive($this->_path, $fullPath);
    }

    public function listFolders()
    {
        return $this->_provider->listFolders($this->_path);
    }

    public function listFoldersRecursive($depth = -1)
    {
        return $this->_provider->listFoldersRecursive($this->_path, $depth);
    }


    public function readFile($path)
    {
        return $this->_provider->readFile($path);
    }

    public function deleteFile($path)
    {
        //$this->_provider->unlinkFile($path);
    }

    public function setPath($path)
    {
        $path .= '/';
        $path = preg_replace('|([\/])?\.\.\/|', '/', $path); // clean path patterns like '/../../'
        $path = preg_replace('|([\/])?\.\/|', '/', $path); // clean path patterns like '/././'
        $path = preg_replace('|[\/]+|', '/', $path); // clean path patterns like '/////path///to//dir///'
        $path = ltrim($path, '/');
        $path = ($path == '/') ? '' : $path;

        //debug("entering path $path");

        $this->_path = $path;
    }

    public function getPath()
    {
        return $this->_path;
    }

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

    public function getBasePath()
    {
        return realpath($this->_provider->basePath()) . DS;
    }

    public function getBaseUrl()
    {
        return rtrim($this->_provider->baseUrl(), '/');
    }

    public function getFileUrl($filePath)
    {
        //@TODO sanitize file path
        //if (strpos($filePath, '..') !== false) {
        //    return;
        //}
        $filePath = trim($filePath, '/');
        return $this->getBaseUrl() . '/' . $filePath;
    }

    public function getSelectListRecursive()
    {
        $files = $this->listFilesRecursive(false);
        $list = [];
        array_walk($files, function ($val, $idx) use (&$list) {
            $list[$val] = $this->getFileUrl($val);
        });
        return $list;
    }

    public function getSelectFolderListRecursive()
    {
        $files = $this->listFoldersRecursive();
        $list = [];
        array_walk($files, function ($val, $idx) use (&$list) {
            $list[$val] = $val;
        });
        return $list;
    }

    public function getSelectListRecursiveGrouped()
    {
        $folders = $this->listFoldersRecursive();
        $list = [];

        foreach ($folders as $folder) {
            $this->open($folder);
            $files = $this->listFiles();

            if (empty($files)) {
                continue;
            }

            $list[$folder] = [];
            array_walk($files, function ($val, $idx) use (&$list, $folder) {
                $list[$folder][$val] = $this->getFileUrl($val);
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
