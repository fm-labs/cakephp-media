<?php
namespace Media\Lib\Media\Provider;

use Cake\Filesystem\Folder;
use Cake\Routing\Router;
use Media\Lib\Media\MediaException;

/**
 * Class LocalStorageProvider
 *
 * MediaProvider for local file systems
 *
 * @package Media\Lib\Media\Provider
 */
class LocalStorageProvider extends MediaProvider
{
    protected $_defaultConfig = [
        'path' => MEDIA,
        'url' => false
    ];

    public function connect()
    {
        if (!is_dir($this->config('path')) || !is_readable($this->config('path'))) {
            throw new MediaException(__("LocalStorage: Root path *{0}* is not accessible", $this->config('path')));
        }
    }

    public function disconnect()
    {
    }

    public function basePath() {
        return $this->_getRealPath('');
    }

    public function baseUrl($full = false)
    {
        $baseUrl = $this->config('url');
        if (!$baseUrl) {
            $baseUrl = ['plugin' => 'Media', 'controller' => 'Media', 'action' => 'index'];
        }
        return Router::url($baseUrl, $full);
    }

    public function listFiles($path)
    {
        $path = $this->_normalizePath($path);
        $folderPath = $this->_getRealPath($path);
        $folder = new Folder($folderPath);

        list(,$files) = $folder->read();
        array_walk($files, function (&$file, $idx) use ($path) {
            $file = $path . $file;
        });
        return $files;
    }

    public function listFilesRecursive($path, $fullPath = false)
    {
        $path = $this->_normalizePath($path);
        $folderPath = $this->_getRealPath($path);

        $folder = new Folder($folderPath);
        $files = $folder->findRecursive($pattern = '.*', $sort = true);

        if ($fullPath !== true) {
            array_walk($files, function (&$file, $idx) use ($folderPath) {
                $file = substr($file, strlen($folderPath));
            });
        }
        return $files;
    }

    public function listFolders($path)
    {
        $path = $this->_normalizePath($path);
        $folderPath = $this->_getRealPath($path);

        $folder = new Folder($folderPath);
        list($dirs,) = $folder->read();
        return $dirs;
    }


    public function listFoldersRecursive($path, $depth = -1)
    {
        $path = $this->_normalizePath($path);
        $folderPath = $this->_getRealPath($path);

        $folder = new Folder($folderPath);
        list($dirs,) = $folder->read();

        $list = [];
        array_walk($dirs, function (&$dir, $idx) use (&$list, &$path, &$depth) {
            $_dir = $path . $dir;
            $list[] = $_dir;

            if ($depth > -1 && $depth == 0) {
                return;
            }

            foreach ($this->listFoldersRecursive($_dir, $depth - 1) as $dir) {
                $list[] = $dir;
            }

        });
        return $list;
    }

    public function readFile($path)
    {
        // TODO: Implement readFile() method.
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
        $path = trim($path, '/');
        return ($path) ? $path . '/' : '';
    }

    /**
     * Real path to file/folder
     *
     * @param $path
     * @return string
     */
    protected function _getRealPath($path)
    {
        $path = $this->_normalizePath($path);
        $realpath = $this->config('path') . $path;

        //return realpath($realpath);
        return $realpath;
    }
}