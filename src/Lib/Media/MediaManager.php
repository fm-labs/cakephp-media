<?php
declare(strict_types=1);

namespace Media\Lib\Media;

use Cake\Core\App;
use Cake\Core\StaticConfigTrait;
use Media\Lib\Media\Provider\MediaProviderInterface;

class MediaManager
{
    use StaticConfigTrait;

    /**
     * @var \Media\Lib\Media\Provider\MediaProviderInterface
     */
    protected $_provider;

    /**
     * @var string Current working dir
     */
    protected $_path;

    /**
     * @param string|array $config Provider config
     * @return \Media\Lib\Media\Provider\MediaProviderInterface
     * @throws \Exception
     */
    public static function getProvider($config): MediaProviderInterface
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
            deprecationWarning(sprintf("The parameter 'url' is deprecated. Use 'baseUrl' instead."));
            $config['baseUrl'] = $config['url'];
            unset($config['url']);
        }
        if (isset($config['path'])) {
            deprecationWarning(sprintf("The parameter 'path' is deprecated. Use 'basePath' instead."));
            $config['basePath'] = $config['path'];
            unset($config['path']);
        }

        //debug($config);

        $provider = $config['className'];
        if (!$provider) {
            throw new \Exception('Provider not configured');
        }

        $className = App::className($provider, 'Lib/Media/Provider', 'Provider');
        if (!$className) {
            throw new \Exception('Provider class not found');
        }

        $providerObj = new $className($config);
        if (!($providerObj instanceof MediaProviderInterface)) {
            throw new \Exception('Provider is not a valid MediaProviderInterface');
        }

        return $providerObj;
    }

    /**
     * @param string $config Media config name
     * @return static
     * @throws \Exception
     */
    public static function get(string $config = 'default'): self
    {
        $provider = self::getProvider($config);

        return new self($provider);
    }

    /**
     * MediaManager constructor.
     *
     * @param \Media\Lib\Media\Provider\MediaProviderInterface $provider
     */
    public function __construct(MediaProviderInterface $provider)
    {
        //$this->mount('default', new LocalStorageProvider(MEDIA));
        //$this->mount('dropbox', new DropboxProvider());
        $this->_provider = $provider;
        //$this->_provider->connect();
        //$this->open('/');
    }

    /**
     * @param string $path Path to media
     * @return array
     * @deprecated
     */
    public function read(string $path): array
    {
        $this->setPath($path);

        return $this->_provider->read($this->_path);
    }

    /**
     * @param string $path Path to media
     * @return $this
     * @deprecated
     */
    public function setPath(string $path)
    {
        $this->_path = $this->_normalizePath($path);

        return $this;
    }

    /**
     * @return string
     * @deprecated
     */
    public function getPath(): string
    {
        return $this->_path;
    }

    /**
     * @return string
     * @deprecated
     */
    public function getParentPath(): string
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

    /**
     * @param string $path Path to media
     * @return array
     */
    public function listFiles(string $path): array
    {
        $path = $this->_normalizePath($path);
        [, $files] = $this->read($path);
        array_walk($files, function (&$file) use ($path) {
            $file = $path . $file;
        });

        return $files;
    }

    /**
     * @param string $path Path to media
     * @return array
     */
    public function listFileUrls(string $path): array
    {
        $urls = [];
        $files = $this->listFiles($path);
        array_walk($files, function ($val) use (&$urls) {
            $urls[$val] = $this->buildFileUrl($val);
        });

        return $urls;
    }

    /**
     * @param string $path Path to media
     * @param bool $fullPath Full path flag
     * @return array
     */
    public function listFilesRecursive(string $path, bool $fullPath = false): array
    {
        $list = [];
        $basePath = $fullPath ? $this->getBasePath() : '';

        $path = $this->_normalizePath($path);
        [$files, $dirs] = $this->read($path);
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

    /**
     * @param string $path Path to media
     * @return array
     */
    public function listFolders(string $path): array
    {
        [$dirs, ] = $this->read($path);

        return $dirs;
    }

    /**
     * @param string $path Path to media
     * @param int $depth Folder depth
     * @return array
     */
    public function listFoldersRecursive(string $path, int $depth = -1): array
    {
        $path = $this->_normalizePath($path);
        [$dirs, ] = $this->read($path);

        $list = [];
        array_walk($dirs, function (&$dir) use (&$list, &$path, &$depth) {
            //$_dir = $this->_normalizePath($path . $dir);
            $_dir = $path . $dir;
            $list[] = $_dir;

            if ($depth > -1 && $depth == 0) {
                return [];
            }

            foreach ($this->listFoldersRecursive($_dir . '/', $depth - 1) as $dir) {
                $list[] = $dir;
            }
        });

        return $list;
    }

    /**
     * Normalize Path
     *
     * Strip leading path separator
     * Append trailing path separator if not root path
     *
     * @param string $path Path to media
     * @return string
     */
    protected function _normalizePath(string $path): string
    {
        $path = trim(trim($path), '/') . '/';
        $path = preg_replace('|([\/])?\.\.\/|', '/', $path); // clean path patterns like '/../../'
        $path = preg_replace('|([\/])?\.\/|', '/', $path); // clean path patterns like '/././'
        $path = preg_replace('|[\/]+|', '/', $path); // clean path patterns like '/////path///to//dir///'
        $path = ltrim($path, '/');
        $path = $path == '/' ? '' : $path;

        return $path;
    }

    /**
     * @param string $filePath File path
     * @return string
     */
    public function buildFileUrl(string $filePath): string
    {
        //@TODO sanitize file path
        //if (strpos($filePath, '..') !== false) {
        //    return;
        //}
        $filePath = trim($filePath, '/');

        return $this->getBaseUrl() . '/' . $filePath;
    }

    /**
     * @param string $filePath File path
     * @return string
     */
    public function buildFileUrlEncoded(string $filePath): string
    {
        $url = urlencode($filePath);
        $url = preg_replace('/\%2F/', '/', $url);
        $url = ltrim($url, '/');

        return $this->getBaseUrl() . '/' . $url;
    }

    /**
     * @param string $filePath File path
     * @return string
     * @deprecated Use buildFileUrl() instead
     */
    public function getFileUrl(string $filePath): string
    {
        return $this->buildFileUrl($filePath);
    }

    /**
     * @param string $filePath File path
     * @return string
     * @deprecated Use buildFileUrlEncoded() instead
     */
    public function getFileUrlEncoded(string $filePath): string
    {
        return $this->buildFileUrlEncoded($filePath);
    }

    /**
     * @return string
     * @deprecated
     */
    public function getBasePath(): string
    {
        return realpath($this->_provider->getConfig('basePath')) . DS;
    }

    /**
     * @return string
     * @deprecated
     */
    public function getBaseUrl(): string
    {
        return rtrim($this->_provider->getConfig('baseUrl'), '/');
    }

    /**
     * @param string $path Path to media
     * @return array
     */
    public function getSelectListRecursive(string $path = '/'): array
    {
        $files = $this->listFilesRecursive($path, false);
        $list = [];
        array_walk($files, function ($val) use (&$list) {
            $list[$val] = $this->buildFileUrl($val);
        });

        return $list;
    }

    /**
     * @param string $path Path to media
     * @return array
     */
    public function getSelectFolderListRecursive(string $path = '/'): array
    {
        $files = $this->listFoldersRecursive($path);
        $list = [];
        array_walk($files, function ($val) use (&$list) {
            $list[$val] = $val;
        });

        return $list;
    }

    /**
     * @param string $path Path to media
     * @return array
     */
    public function getSelectListRecursiveGrouped($path = '/'): array
    {
        $folders = $this->listFoldersRecursive($path);
        $list = [];

        foreach ($folders as $folder) {
            $files = $this->listFiles($folder);

            if (empty($files)) {
                continue;
            }

            $list[$folder] = [];
            array_walk($files, function ($val) use (&$list, $folder) {
                $list[$folder][$val] = $this->buildFileUrl($val);
            });
        }

        return $list;
    }
}
