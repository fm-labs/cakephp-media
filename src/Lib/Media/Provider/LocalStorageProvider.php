<?php
/**
 * Created by PhpStorm.
 * User: flow
 * Date: 9/5/15
 * Time: 5:23 PM
 */

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
        $_path = $this->_getRealPath($path);
        $folder = new Folder($_path);
        list(,$files) = $folder->read();
        array_walk($files, function (&$val, $idx) use ($path) {
            $val = $path . DS . $val;
        });
        return $files;
    }

    public function listFilesRecursive($path, $fullPath = false)
    {
        $_path = $this->_getRealPath($path);
        $folder = new Folder($_path);
        $files = $folder->findRecursive($pattern = '.*', $sort = true);
        if ($fullPath !== true) {
            array_walk($files, function (&$val, $idx) use ($_path) {
                $val = substr($val, strlen($_path) - 1);
            });
        }
        return $files;
    }

    public function listFolders($path)
    {
        $_path = $this->_getRealPath($path);
        $folder = new Folder($_path);
        list($dirs,) = $folder->read();
        array_walk($dirs, function (&$val, $idx) use ($path) {
            $val = $path . DS . $val;
        });
        return $dirs;
    }


    public function listFoldersRecursive($path)
    {
        $_path = $this->_getRealPath($path);
        $folder = new Folder($_path);
        list($dirs,) = $folder->read();

        $list = [];
        array_walk($dirs, function (&$val, $idx) use (&$list, $path) {
            $list[] = $path . DS . $val;

            foreach ($this->listFoldersRecursive($path . DS . $val) as $dir) {
                $list[] = $dir;
            }
        });
        return $list;
    }


    public function readFile($path)
    {
        // TODO: Implement readFile() method.
    }

    protected function _getRealPath($path)
    {
        $path = rtrim($path, '/');
        return $this->config('path') . DS . $path;
    }
}