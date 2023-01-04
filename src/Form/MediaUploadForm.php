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
    protected $mediaConfig;

    /**
     * @var array Uploader config
     */
    protected $uploaderConfig;

    /**
     * @var \Upload\Uploader The Uploader instance
     */
    protected $uploader;

    /**
     * @var array Form data
     */
    protected $data = [];

    /**
     * @var array
     */
    protected $_result;

    /**
     * @param array|string $mediaConfig
     * @param array|string|\Upload\Uploader $uploaderConfig
     */
    public function __construct($mediaConfig = 'default', $uploaderConfig = [])
    {
        $this->mediaConfig = $mediaConfig;
        $this->uploaderConfig = $uploaderConfig;
    }

    /**
     * Build upload form schema
     *
     * @param \Cake\Form\Schema $schema
     * @return \Cake\Form\Schema
     */
    protected function _buildSchema(Schema $schema): Schema
    {
        $schema->addField('upload_file', ['type' => 'string']);

        return $schema;
    }

    /**
     * @return \Upload\Uploader
     * @throws \Exception
     */
    public function getUploader(): Uploader
    {
        if (!$this->uploader) {
            if (is_array($this->uploaderConfig) || is_string($this->uploaderConfig)) {
                $this->uploader = new Uploader($this->uploaderConfig);
            } elseif ($this->uploaderConfig instanceof Uploader) {
                $this->uploader = $this->uploaderConfig;
            } else {
                throw new \InvalidArgumentException('Invalid uploader config');
            }
        }

        return $this->uploader;
    }

    /**
     * Process upload
     *
     * @param array $data Form data
     * @return bool
     * @throws \Exception
     */
    public function execute(array $data = [], array $options = []): bool
    {
        $this->_result = $this->getUploader()->upload($data['upload_file']);

        return $this->_result ? true : false;
    }

    /**
     * Get the result of the upload.
     *
     * @return array
     */
    public function getUploadedFiles(): array
    {
        return $this->_result;
    }
}
