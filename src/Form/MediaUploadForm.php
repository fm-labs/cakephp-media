<?php

namespace Media\Form;

use Cake\Form\Form;
use Cake\Form\Schema;
use Upload\Uploader;

class MediaUploadForm extends Form
{

    /**
     * @var Uploader
     */
    protected $uploader;

    /**
     * Form data
     *
     * @var array
     */
    protected $data = [];


    /**
     * @param $mediaConfig
     * @param array|Uploader $uploader
     */
    public function __construct($mediaConfig, $uploader = [])
    {
        $this->config = $mediaConfig;

        if (is_array($uploader)) {
            $this->uploader = new Uploader($uploader);
        } elseif ($uploader instanceof Uploader) {
            $this->uploader = $uploader;
        } else {
            throw new \InvalidArgumentException('Invalid uploader config');
        }
    }

    /**
     * Build upload form schema
     */
    protected function _buildSchema(Schema $schema)
    {
        $schema->addField('upload_file', ['type' => 'string']);
        return $schema;
    }

    /**
     * Process upload
     */
    public function execute(array $data = [])
    {
        $result = $this->uploader->upload($data['upload_file']);
        return $result;
    }
}
