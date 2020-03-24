<?php
namespace Media\View\Cell;

use Cake\ORM\TableRegistry;
use Cake\View\Cell;
use Media\Lib\Media\MediaManager;

/**
 * ImageSelect cell
 */
class ImageSelectCell extends Cell
{

    /**
     * List of valid options that can be passed into this
     * cell's constructor.
     *
     * @var array
     */
    protected $_validCellOptions = [];

    /**
     * Default display method.
     *
     * @return void
     */

    public function display($params = [])
    {
        $params += [
            'config' => 'images',
            'model' => null,
            'id' => null,
            'scope' => 'image',
            'label' => null,
            'multiple' => false,
        ];

        $Model = TableRegistry::getTableLocator()->get($params['model']);

        $image = null;
        if (isset($params['image'])) {
            $image = $params['image'];
        } else {
            $Model->behaviors()->unload('Media');
            $image = $Model->get($params['id'], [
                'contain' => [],
                'media' => true,
            ]);
        }

        /*
        if ($this->request->is(['patch', 'post', 'put'])) {
            debug($this->request->getData());
            $content = $Model->patchEntity($image, $this->request->getData());
            if ($Model->save($content)) {
                $this->Flash->success(__d('banana','The {0} has been saved.', __d('banana','content')));
            } else {
                $this->Flash->error(__d('banana','The {0} could not be saved. Please, try again.', __d('banana','content')));
            }
        } else {
        }
        */

        $mm = MediaManager::get($params['config']);
        $files = $mm->getSelectListRecursiveGrouped();
        $this->set('imageFiles', $files);

        $this->set($params);

        /**
         * Workaround for a strange/unresolved bug:
         * If this is set before imageFiles, the height option is not present in template !?!?!?!
         */
        $imageOptions = [
            'width' => 200,
            'height' => 200,
        ];
        $this->set('imageOptions', $imageOptions);
    }
}
