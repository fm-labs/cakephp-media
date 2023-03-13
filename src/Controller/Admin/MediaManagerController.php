<?php
declare(strict_types=1);

namespace Media\Controller\Admin;

use Cake\ORM\TableRegistry;
use Media\Lib\Media\MediaManager;

/**
 * Class MediaManagerController
 *
 * @package Media\Controller\Admin
 * @todo MediaManagerController requires a rewrite
 */
class MediaManagerController extends AppController
{
    public function treeData()
    {
        $this->viewBuilder()->setClassName('Json');

        $id = $this->request->getQuery('id');
        $path = $id == '#' ? '/' : $id;
        $treeData = [];
        $config = $this->request->getQuery('config', 'default');

        $mm = MediaManager::get($config);

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

        $config = $this->request->getQuery('config', 'default');

        $mm = MediaManager::get($config);
        $files = $mm->listFiles($path);
        array_walk($files, function ($val) use (&$treeData, &$mm, &$parent) {

            $icon = false;
            $thumbUrl = null;
            $filename = basename($val);
            if (preg_match('/^(.*)\.(jpg|gif|jpeg|png)$/i', $filename)) {
                // use thumbnail as icon
                $icon = $thumbUrl = $mm->getFileUrl($val);
            } elseif (preg_match('/^\./', $filename)) {
                // ignore dot-files
                return;
            }

            $treeData[] = [
                'id' => $val,
                'text' => basename($val),
                'children' => false,
                'type' => 'file',
                'icon' => $icon,
                'thumbUrl' => $thumbUrl,
            ];
        });

        $this->set('treeData', $treeData);
        $this->set('_serialize', 'treeData');
    }

    public function setImage()
    {
        $scope = $this->request->getQuery('scope');
        $multiple = $this->request->getQuery('multiple');
        $model = $this->request->getQuery('model');
        $id = $this->request->getQuery('id');
        $config = $this->request->getQuery('config');

        $Table = TableRegistry::getTableLocator()->get($model);

        $Table->behaviors()->unload('Media');
        $content = $Table->get($id, [
            'contain' => [],
            'media' => true,
        ]);

        $file = $content->get($scope);

        if ($this->request->is(['patch', 'post', 'put'])) {
            $patchFile = $this->request->getData($scope);
            debug($file);
            debug($patchFile);
            if (is_array($patchFile)) {
                $patchFile = $patchFile[0];
            }
            if (is_array($file)) {
                //$files = explode(',', $file);
                $file[] = $patchFile;
                $patchFile = join(',', $file);
            }
            debug($patchFile);

            $content = $Table->patchEntity($content, [$scope => $patchFile]);
            //$content->$scope = $this->request->data[$scope];
            if ($Table->save($content)) {
                $this->Flash->success(__d('media', 'The {0} has been saved.', __d('media', 'content')));
            } else {
                $this->Flash->error(__d('media', 'The {0} could not be saved. Please, try again.', __d('media', 'content')));
            }
        } else {
        }

        $mm = MediaManager::get($config);
        $files = $mm->getSelectListRecursiveGrouped();
        $this->set('imageFiles', $files);
        $this->set('scope', $scope);
        $this->set('multiple', $multiple);
        $this->set('model', $model);
        $this->set('id', $id);
        $this->set('config', $config);

        $this->set(compact('content'));
        $this->set('_serialize', ['content']);
    }

    public function imageSelect()
    {
    }

    public function deleteImage()
    {
        $scope = $this->request->getQuery('scope');
        $multiple = $this->request->getQuery('multiple');
        $model = $this->request->getQuery('model');
        $id = $this->request->getQuery('id');
        $pathEncoded = $this->request->getQuery('img');
        $pathDecoded = base64_decode($pathEncoded);
        $referer = $this->request->getQuery('ref') ?: $this->referer();

        $Table = TableRegistry::getTableLocator()->get($model);

        $Table->behaviors()->unload('Media');
        $content = $Table->get($id, [
            'contain' => [],
            'media' => true,
        ]);

        //if (!in_array($scope, ['teaser_image_file', 'image_file', 'image_files'])) {
        //    throw new BadRequestException('Invalid scope');
        //}

        $updated = '';
        if ($multiple) {
            $file = $content->get($scope);
            if (is_array($file)) {
                $filtered = array_filter($file, function ($filepath) use ($pathEncoded) {
                    //Log::debug('Filter ' . $filepath . '[' . base64_encode($filepath) . '] => ' . $pathEncoded);
                    if (base64_encode($filepath) == $pathEncoded) {
                        return false;
                    }

                    return true;
                });
            }
            $updated = join(',', $filtered);
        }

        $content->setAccess($scope, true);
        $content->set($scope, $updated);

        if ($Table->save($content)) {
            $this->Flash->success(__d('media', 'The {0} has been removed.', $scope));
        } else {
            $this->Flash->error(__d('media', 'The {0} could not be removed. Please, try again.', $scope));
        }

        return $this->redirect($referer);
    }
}
