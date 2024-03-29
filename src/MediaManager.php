<?php
declare(strict_types=1);

namespace Media;

use Cake\Core\App;
use Cake\Core\StaticConfigTrait;
use Exception;
use InvalidArgumentException;
use Media\Lib\Media\Provider\MediaProviderInterface;

class MediaManager
{
    use StaticConfigTrait;

    /**
     * @var \Media\Lib\Media\Provider\MediaProviderInterface
     * @deprecated Will be removed in 1.0
     */
    protected MediaProviderInterface $_provider;

    /**
     * @var string Current working dir
     * @deprecated Will be removed in 1.0
     */
    protected string $_path;

    /**
     * @param array|string $config Provider config
     * @return \Media\Lib\Media\Provider\MediaProviderInterface
     * @throws \Exception
     * @deprecated Will be removed in 1.0
     */
    public static function getProvider($config): MediaProviderInterface
    {
        if (is_string($config) && in_array($config, self::configured())) {
            $config = self::getConfig($config);
        } elseif (!is_array($config)) {
            throw new InvalidArgumentException("Invalid configuration '" . (string)$config . "'");
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
            throw new Exception('Provider not configured');
        }

        $className = App::className($provider, 'Lib/Media/Provider', 'Provider');
        if (!$className) {
            throw new Exception('Provider class not found');
        }

        $providerObj = new $className($config);
        if (!($providerObj instanceof MediaProviderInterface)) {
            throw new Exception('Provider is not a valid MediaProviderInterface');
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
     * @deprecated Will be removed in 1.0
     */
    public function read(string $path): array
    {
//        deprecationWarning("MediaManager::read() is deprecated.");
//        $this->setPath($path);
//        return $this->_provider->read($this->_path);
        return $this->_provider->read($path);
    }

    /**
     * @param string $path Path to media
     * @return $this
     * @deprecated Will be removed in 1.0
     */
    public function setPath(string $path)
    {
        deprecationWarning("MediaManager::setPath() is deprecated.");

        $this->_path = $this->_normalizePath($path);

        return $this;
    }

    /**
     * @return string
     * @deprecated Will be removed in 1.0
     */
    public function getPath(): string
    {
        deprecationWarning("MediaManager::getPath() is deprecated.");

        return $this->_path;
    }

    /**
     * @return string
     * @deprecated Will be removed in 1.0
     */
    public function getParentPath(): string
    {
        deprecationWarning("MediaManager::getParentPath() is deprecated.");

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
        //debug($path . ' -> ' . $this->_normalizePath($path));
        $path = $this->_normalizePath($path);
        [, $files] = $this->read($path);
        array_walk($files, function (&$file) use ($path): void {
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
        array_walk($files, function ($val) use (&$urls): void {
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
        [$dirs, $files] = $this->read($path);
        array_walk($files, function ($file) use (&$list, $basePath, $path): void {
            $list[] = $basePath . $path . $file;
        });

        array_walk($dirs, function ($dir) use (&$list, $path, $fullPath): void {
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
        array_walk($files, function ($val) use (&$list): void {
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
        array_walk($files, function ($val) use (&$list): void {
            $list[$val] = $val;
        });

        return $list;
    }

    /**
     * @param string $path Path to media
     * @return array
     */
    public function getSelectListRecursiveGrouped(string $path = '/'): array
    {
        $folders = $this->listFoldersRecursive($path);
        $list = [];

        foreach ($folders as $folder) {
            $files = $this->listFiles($folder);

            if (empty($files)) {
                continue;
            }

            $list[$folder] = [];
            array_walk($files, function ($val) use (&$list, $folder): void {
                $list[$folder][$val] = $this->buildFileUrl($val);
            });
        }

        return $list;
    }

    /**
     * @throws Exception
     */
    public function createDirectory(string $path): bool
    {
        return $this->_provider->createDirectory($path);
    }

    /**
     * @throws Exception
     */
    public function directoryExists(string $path): bool
    {
        return $this->_provider->directoryExists($path);
    }

    /**
     * @throws Exception
     */
    public function fileExists(string $path): bool
    {
        return $this->_provider->fileExists($path);
    }

    /**
     * @throws Exception
     */
    public function writeFile(string $path, string $contents): bool
    {
        return $this->_provider->writeFile($path, $contents);
    }

    /**
     * @throws Exception
     */
    public function deleteFile(string $path): bool
    {
        return $this->_provider->deleteFile($path);
    }

    /**
     * @throws Exception
     */
    public function renameDirectory(string $path, string $newPath): bool
    {
        if ($this->fileExists($newPath)) {
            throw new MediaException("File already exists");
        }
        if ($this->directoryExists($newPath)) {
            throw new MediaException("Folder already exists");
        }
        return $this->_provider->move($path, $newPath);
    }

    /**
     * @throws Exception
     */
    public function renameFile(string $path, string $newPath): bool
    {
        if ($this->fileExists($newPath)) {
            throw new MediaException("File already exists: $newPath");
        }
        if ($this->directoryExists($newPath)) {
            throw new MediaException("Folder already exists");
        }
        return $this->_provider->move($path, $newPath);
    }

}
