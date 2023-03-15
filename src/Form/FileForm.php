<?php
declare(strict_types=1);

namespace Media\Form;

use Cake\Event\EventManager;
use Cake\Form\Form;
use Cake\Form\Schema;
use Cake\Validation\Validator;
use Media\MediaManager;

/**
 * NewFolder Form.
 */
abstract class FileForm extends Form
{
    private ?MediaManager $mediaManager = null;

    public function __construct(?EventManager $eventManager = null)
    {
        parent::__construct($eventManager);
    }

    /**
     * Builds the schema for the modelless form
     *
     * @param \Cake\Form\Schema $schema From schema
     * @return \Cake\Form\Schema
     */
    protected function _buildSchema(Schema $schema): Schema
    {
        $schema->addField('config', [
            'required' => true
        ]);
        $schema->addField('path', [
            'required' => true
        ]);
        $schema->addField('file', [
            'required' => true
        ]);
        return $schema;
    }

    /**
     * Form validation builder
     *
     * @param \Cake\Validation\Validator $validator to use against the form
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator): Validator
    {
        $validator->notEmptyString('config');
        $validator->notEmptyString('path');
        $validator->notEmptyString('file');
        return $validator;
    }

    /**
     * @param string $config
     * @return MediaManager
     * @throws \Exception
     */
    public function getMediaManager(string $config): MediaManager
    {
        if (!$this->mediaManager) {
            $this->mediaManager = MediaManager::get($config);
        }
        return $this->mediaManager;
    }
}
