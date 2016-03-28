<?php
namespace Media\Controller\Admin;


use Cake\ORM\TableRegistry;
use Media\Lib\Media\MediaManager;

class MediaManagerController extends AppController
{

    public function treeData()
    {
        $this->viewBuilder()->className('Json');

        $id = $this->request->query('id');
        $path = ($id == '#') ? '/' : $id;
        $treeData = [];
        $config = $this->request->query('config');

        $mm = MediaManager::get($config);
        $mm->open($path);

        $folders = $mm->listFoldersRecursive(0);
        array_walk($folders, function ($val) use (&$treeData, &$id) {
            $treeData[] = [
                'id' => $val,
                'text' => basename($val),
                'children' => true,
                'type' => 'folder',
                'parent' => $id
            ];
        });

        /*
        $files = $mm->listFiles();
        array_walk($files, function ($val) use (&$treeData, &$mm, &$parent) {
            $treeData[] = ['id' => $val, 'text' => basename($val), 'children' => false, 'type' => 'file', 'icon' => $mm->getFileUrl($val)];
        });
        */


        $this->set('treeData', $treeData);
        $this->set('_serialize', 'treeData');
    }


    public function filesData()
    {
        $this->viewBuilder()->className('Json');

        $id = $this->request->query('id');
        $path = ($id == '#') ? '/' : $id;
        $treeData = [];

        $config = $this->request->query('config');

        $mm = MediaManager::get($config);
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
                'icon' => $icon
            ];
        });


        $this->set('treeData', $treeData);
        $this->set('_serialize', 'treeData');
    }

    public function setImage()
    {
        $scope = $this->request->query('scope');
        $multiple = $this->request->query('multiple');
        $model = $this->request->query('model');
        $id = $this->request->query('id');
        $config = $this->request->query('config');

        $Table = TableRegistry::get($model);
        
        $Table->behaviors()->unload('Media');
        $content = $Table->get($id, [
            'contain' => [],
            'media' => true,
        ]);

        if ($this->request->is(['patch', 'post', 'put'])) {
            $content = $Table->patchEntity($content, $this->request->data);
            //$content->$scope = $this->request->data[$scope];
            if ($Table->save($content)) {
                $this->Flash->success(__d('banana','The {0} has been saved.', __d('banana','content')));
            } else {
                $this->Flash->error(__d('banana','The {0} could not be saved. Please, try again.', __d('banana','content')));
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



    public function deleteImage()
    {
        $scope = $this->request->query('scope');
        $multiple = $this->request->query('multiple');
        $model = $this->request->query('model');
        $id = $this->request->query('id');
        $referer = ($this->request->query('ref')) ?: $this->referer();

        $Table = TableRegistry::get($model);

        $Table->behaviors()->unload('Media');
        $content = $Table->get($id, [
            'contain' => [],
            'media' => true,
        ]);

        //if (!in_array($scope, ['teaser_image_file', 'image_file', 'image_files'])) {
        //    throw new BadRequestException('Invalid scope');
        //}

        $content->accessible($scope, true);
        $content->set($scope, '');

        if ($Table->save($content)) {
            $this->Flash->success(__d('banana','The {0} has been removed.', $scope));
        } else {
            $this->Flash->error(__d('banana','The {0} could not be removed. Please, try again.', $scope));
        }
        return $this->redirect($referer);
    }
    
}