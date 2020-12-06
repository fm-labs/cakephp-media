<?php
declare(strict_types=1);

namespace Media\Controller\Admin;

use Cake\Core\Plugin;
use Upload\Uploader;

/**
 * Class UploadController
 *
 * @package Media\Controller\Admin
 */
class UploadController extends AppController
{
    /**
     * @return void
     */
    public function index(): void
    {
        if (!Plugin::isLoaded('Upload')) {
            $this->Flash->error(__('Plugin `{0}` not installed', 'Upload'));
            $this->redirect($this->referer('/'));
        }

        try {
            //@TODO Load default media uploader configuration
            $uploader = new Uploader([
                'uploadDir' => MEDIA . 'uploads/',
                'minFileSize' => 1,
                'maxFileSize' => 2097152, // 2MB
                'mimeTypes' => 'image/*',
                'fileExtensions' => 'gif,jpeg,jpg,png',
                'multiple' => false,
                'slug' => '_',
                'hashFilename' => false,
                'uniqueFilename' => true,
                'overwrite' => false,
                'saveAs' => null, // filename override
                //'pattern' => false, // @todo Implement me
            ]);
            //$uploader->setSaveAs('hellyea.jpg');

            if ($this->request->is('post')) {
                $upload = $uploader->upload($this->request->getData('upload'));
                $this->set(compact('upload'));
            }
        } catch (\Exception $ex) {
            $this->Flash->error($ex->getMessage());
        }
    }
}
