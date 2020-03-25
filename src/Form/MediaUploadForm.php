<?php
declare(strict_types=1);

namespace Media\Form;

use Cake\Form\Form;
use Cake\Form\Schema;
use Upload\Uploader;

class MediaUploadForm extends Form
{
    /**
     * @var string Name of media config
     */
    protected $config;

    /**
     * @var \Upload\Uploader The Uploader instance
     */
    protected $uploader;

    /**
     * @var array Form data
     */
    protected $data = [];

    /**
     * @param $mediaConfig
     * @param array|\Upload\Uploader $uploader
     * @throws \InvalidArgumentException
     */
    public function __construct($mediaConfig = 'default', $uploader = [])
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
    protected function _buildSchema(Schema $schema): Schema
    {
        $schema->addField('upload_file', ['type' => 'string']);

        return $schema;
    }

    /**
     * Process upload
     * @param array $data Form data
     * @return array|bool
     */
    public function execute(array $data = []): bool
    {
        $result = $this->uploader->upload($data['upload_file']);

        return $result ? true : false;
    }
}
