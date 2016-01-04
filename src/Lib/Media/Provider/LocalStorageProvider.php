<?php
namespace Media\Lib\Media\Provider;

use Cake\Filesystem\Folder;
use Media\Lib\Media\MediaException;

class LocalStorageProvider extends MediaProvider
{
    protected $_defaultConfig = [
        'path' => MEDIA,
        'url' => false
    ];

    public function connect()
    {
        if (!is_dir($this->config('path')) || !is_readable($this->config('path'))) {
            throw new MediaException(__("LocalStorage: Root path for *{0}* is not accessible", $this->config('label')));
        }
    }

    public function disconnect()
    {
        // nothing to do
    }

    public function listFiles($path)
    {
        $path = $this->_normalizePath($path);
        $folderPath = $this->_getRealPath($path);
        $folder = new Folder($folderPath);
        list(,$files) = $folder->read();
        array_walk($files, function (&$file, $idx) use ($path) {
            $file = ($path) ? $path . '/' . $file : $file;
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
        array_walk($dirs, function (&$dir, $idx) use ($path) {
            $dir = $path . $dir;
        });
        return $dirs;
    }


    public function listFoldersRecursive($path)
    {
        $path = $this->_normalizePath($path);
        $folderPath = $this->_getRealPath($path);
        $folder = new Folder($folderPath);
        list($dirs,) = $folder->read();

        $list = [];
        array_walk($dirs, function (&$dir, $idx) use (&$list, $path) {
            $list[] = ($path) ? $path . '/' . $dir : $dir;

            foreach ($this->listFoldersRecursive($path . '/' . $dir) as $dir) {
                $list[] = $dir;
            }

        });
        return $list;
    }


    public function readFile($path)
    {
        // TODO: Implement readFile() method.
    }

    protected function _normalizePath($path)
    {
        $path = trim($path, '/');
        return $path;
    }

    protected function _getRealPath($path)
    {
        $path = $this->_normalizePath($path);
        $realpath = ($path) ? $this->config('path') . $path . DS : $this->config('path');

        //return realpath($realpath) . DS;
        return $realpath;
    }
}