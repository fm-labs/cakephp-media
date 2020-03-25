<?php
declare(strict_types=1);

/**
 * Created by PhpStorm.
 * User: flow
 * Date: 9/5/15
 * Time: 5:19 PM
 */

namespace Media\Controller\Admin;

use Cake\Core\Configure;
use Cake\Core\Plugin;
use Cake\Routing\Router;
use Media\Lib\Media\MediaManager;

/**
 * @deprecated Use FilesController instead
 */
class MediaBrowserController extends AppController
{
    /**
     * @var \Media\Lib\Media\MediaManager
     */
    protected $_mm;

    /**
     * @var string Name of media configuration
     */
    protected $_mediaConfig;

    public function initialize(): void
    {
        parent::initialize();

        $this->loadComponent('RequestHandler');

        //$this->layout = "Backend.media_browser";

        $config = $this->request->getParam('config');
        if (!$config) {
            $config = $this->request->getQuery('config');
        }
        if (!$config) {
            $config = "default";
        }
        $configKey = 'Media.' . $config;
        if (!Plugin::isLoaded('Media') || !Configure::check($configKey)) {
            $this->request = $this->request
                ->withParam('action', 'noconfig')
                ->withParam('config', $config);
        } else {
            $this->_mediaConfig = $config;
            $this->_mm = MediaManager::get($config);
        }
    }

    public function beforeFilter(\Cake\Event\EventInterface $event)
    {
        parent::beforeFilter($event);
    }

    public function beforeRender(\Cake\Event\EventInterface $event)
    {
        parent::beforeRender($event);

        if ($this->_mediaConfig) {
            $this->set('cfg', $this->_mediaConfig);
            $this->set('currentPath', $this->_mm->getPath());
            $this->set('parentPath', $this->_mm->getParentPath());
        }
    }

    public function noconfig()
    {
        $pluginLoaded = Plugin::isLoaded('Media');
        $config = $this->request->getParam('config');
        if ($pluginLoaded) {
            $configExample = @file_get_contents(Plugin::path('Media') . DS . 'config' . DS . 'media.default.php');
        } else {
            $configExample = "Media plugin must be loaded to show example configuration";
        }

        $this->set('pluginLoaded', $pluginLoaded);
        $this->set('configName', $config);
        $this->set('configExample', $configExample);
    }

    public function index()
    {
        $this->redirect(['action' => 'browse']);
    }

    public function browse()
    {
        $path = $this->request->getQuery('path');
        $file = $this->request->getQuery('file');
        $this->_mm->open($path);

        $this->set('directories', $this->_mm->listFolders());
        $this->set('files', $this->_mm->listFiles());
        $this->render('index');
    }

    public function treeData()
    {
        $this->viewBuilder()->setClassName('Json');

        $id = $this->request->getQuery('id');
        $path = $id == '#' ? '/' : $id;
        $treeData = [];

        $mm =& $this->_mm;
        $mm->open($path);

        $folders = $mm->listFoldersRecursive($path, 0);
        array_walk($folders, function ($val) use (&$treeData, &$id) {
            $treeData[] = [
                'id' => $val,
                'text' => basename($val),
                'children' => true,
                'type' => 'folder',
                'parent' => $id,
            ];
        });

        /*
        $files = $mm->listFiles();
        array_walk($files, function ($val) use (&$treeData, &$mm, &$parent) {
            $treeData[] = ['id' => $val, 'text' => basename($val), 'children' => false, 'type' => 'file', 'data-icon' => $mm->getFileUrl($val)];
        });
        */

        $this->set('treeData', $treeData);
        $this->set('_serialize', 'treeData');
    }

    public function filesData()
    {
        $this->viewBuilder()->setClassName('Json');

        $id = $this->request->getQuery('id');
        $path = $id == '#' ? '/' : $id;
        $treeData = [];

        $mm =& $this->_mm;
        $mm->open($path);

        $files = $mm->listFiles();
        array_walk($files, function ($val) use (&$treeData, &$mm, &$parent) {

            $icon = true;
            $filename = basename($val);
            if (preg_match('/^(.*)\.(jpg|gif|jpeg|png)$/i', $filename)) {
                // use thumbnail as icon
                $icon = $mm->getFileUrl($val);
            } elseif (preg_match('/^\./', $filename)) {
                // ignore dot-files
                return;
            }

            $treeData[] = [
                'id' => $val,
                'text' => basename($val),
                'children' => false,
                'type' => 'file',
                'data-icon' => $icon,
                'actions' => [
                    //['title' => 'View', 'data-icon' => 'eye', 'url' => Router::url(['action' => 'view', 'path' => $val ])],
                    //['title' => 'Edit', 'data-icon' => 'edit', 'url' => Router::url(['action' => 'edit', 'path' => $val ])],
                    ['title' => 'Download', 'data-icon' => 'download', 'url' => Router::url(['action' => 'download', 'path' => $val ])],
                    //['title' => 'Download', 'data-icon' => 'download', 'action' => 'download' ])]
                ],
            ];
        });

        $this->set('treeData', $treeData);
        $this->set('_serialize', 'treeData');
    }

    public function filepicker()
    {
        $path = $this->request->getQuery('path');
        $file = $this->request->getQuery('file');
        $this->_mm->open($path);

        $this->set('folders', $this->_mm->listFolders());
        $this->set('files', $this->_mm->listFiles());
    }

    /**
     * @deprecated
     */
    public function treeFiles()
    {
        $this->viewBuilder()->setClassName('Json');

        $files = [];
        $selectedDirs = $this->request->getData('selected');
        foreach ($selectedDirs as $dir) {
            $this->_mm->open($dir);
            $files += $this->_mm->listFileUrls();
        }

        $treeData = [];
        array_walk($files, function ($val) use (&$treeData) {
            $treeData[] = ['id' => $val, 'text' => basename($val), 'data-icon' => 'file'];
        });

        $this->set('treeData', $treeData);
        $this->set('_serialize', 'treeData');
    }
}
