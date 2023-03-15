<?php
declare(strict_types=1);

namespace Media\Lib\Media\Provider;

use Cake\Log\Log;
use League\Flysystem\FilesystemException;
use League\Flysystem\UnableToCheckExistence;
use League\Flysystem\UnableToCreateDirectory;
use League\Flysystem\UnableToDeleteDirectory;
use League\Flysystem\UnableToDeleteFile;
use League\Flysystem\UnableToMoveFile;
use League\Flysystem\UnableToWriteFile;

/**
 * Class LocalStorageProvider
 *
 * MediaProvider for local file systems
 *
 * @package Media\Lib\Media\Provider
 */
class FilesystemProvider extends AbstractMediaProvider
{
    protected array $_defaultConfig = [
        'basePath' => null,
        'baseUrl' => null,
    ];

    /**
     * @var string|null Absolute path to local directory
     */
    protected ?string $_basePath;

    /**
     * @var string|null Base URL
     */
    protected ?string $_baseUrl;

    /**
     * @var \Media\Filesystem\FilesystemInterface|\League\Flysystem\FilesystemOperator|null
     */
    protected \Media\Filesystem\FilesystemInterface|\League\Flysystem\FilesystemOperator|null $_filesystem;

    public function initialize()
    {
        $basePath = $this->getConfig('basePath');
        if (!$basePath) {
            throw new \InvalidArgumentException("LocalStorage: Base path not defined");
        }
//        if (!is_dir($basePath)) {
//            throw new \Exception("LocalStorage: Base path not found: " . $basePath);
//        }
//        if (!is_readable($basePath)) {
//            throw new \Exception(__d('media', "LocalStorage: Root path *{0}* is not accessible", $basePath));
//        }
        $this->_basePath = rtrim($basePath, '/') . '/';
        $this->_baseUrl = $this->getConfig('baseUrl');

        $localAdapter = new \League\Flysystem\Local\LocalFilesystemAdapter($this->_basePath);
        $this->_filesystem = new \League\Flysystem\Filesystem($localAdapter);
    }

    /**
     * Read contents of directory path
     * @param $path string Path to directory
     * @return array List of files and directories
     * @throws \Exception
     * @throws \League\Flysystem\FilesystemException
     */
    public function read($path = "/"): array
    {
        $recursive = false;
        $files = [];
        $dirs = [];
        $path = trim($path, '/');
        try {
            $listing = $this->_filesystem->listContents($path, $recursive)->sortByPath();

            /** @var \League\Flysystem\StorageAttributes $item */
            foreach ($listing as $item) {
                $_path = $item->path();
                $_path = substr($_path, strlen($path)); // strip the path prefix from the path relative to the base path
                $_path = trim($_path, '/'); // strip any leading and trailing slashes

                if ($item instanceof \League\Flysystem\FileAttributes) {
                    $files[] = $_path;
                } elseif ($item instanceof \League\Flysystem\DirectoryAttributes) {
                    $dirs[] = $_path;
                }
            }
        } catch (FilesystemException $exception) {
            $this->log($path, $exception->getMessage());
        }

        return [$dirs, $files];
    }

    /**
     * @throws FilesystemException
     */
    public function createDirectory(string $path): bool
    {
        try {
            $this->_filesystem->createDirectory($path);
        } catch (FilesystemException | UnableToCreateDirectory $exception) {
            $this->log($path, $exception->getMessage());
            throw $exception;
        }

        return true;
    }

    /**
     * @throws FilesystemException
     */
    public function directoryExists(string $path): bool
    {
        try {
            return $this->_filesystem->directoryExists($path);
        } catch (FilesystemException | UnableToCheckExistence $exception) {
            $this->log($path, $exception->getMessage());
            throw $exception;
        }
    }

    /**
     * @throws FilesystemException
     */
    public function fileExists(string $path): bool
    {
        try {
            return $this->_filesystem->has($path);
        } catch (FilesystemException | UnableToCheckExistence $exception) {
            $this->log($path, $exception->getMessage());
            throw $exception;
        }
    }

    /**
     * @throws FilesystemException
     */
    public function deleteDirectory(string $path): bool
    {
        try {
            $this->_filesystem->deleteDirectory($path);
        } catch (FilesystemException | UnableToDeleteDirectory $exception) {
            $this->log($path, $exception->getMessage());
            throw $exception;
        }

        return true;
    }

    /**
     * @throws FilesystemException
     */
    public function writeFile(string $path, string $contents): bool
    {
        try {
            $this->_filesystem->write($path, $contents);
        } catch (FilesystemException | UnableToWriteFile $exception) {
            $this->log($path, $exception->getMessage());
            throw $exception;
        }

        return true;
    }

    /**
     * @throws FilesystemException
     */
    public function deleteFile(string $path): bool
    {
        try {
            $this->_filesystem->delete($path);
        } catch (FilesystemException | UnableToDeleteFile $exception) {
            $this->log($path, $exception->getMessage());
            throw $exception;
        }

        return true;
    }

    /**
     * @throws FilesystemException
     */
    public function move(string $source, string $destination): bool
    {
        try {
            $this->_filesystem->move($source, $destination);
        } catch (FilesystemException | UnableToMoveFile $exception) {
            $this->log($path, $exception->getMessage());
            throw $exception;
        }

        return true;
    }

    protected function log($path, $msg, $level = 'error'): void
    {
        Log::write($level, sprintf('[filesystem][%s] %s', $path, $msg));
    }
}
