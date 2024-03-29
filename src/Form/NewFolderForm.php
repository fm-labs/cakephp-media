<?php
declare(strict_types=1);

namespace Media\Form;

use Cake\Form\Schema;
use Cake\Validation\Validator;

/**
 * NewFolderForm.
 */
class NewFolderForm extends FolderForm
{

    /**
     * Builds the schema for the modelless form
     *
     * @param \Cake\Form\Schema $schema From schema
     * @return \Cake\Form\Schema
     */
    protected function _buildSchema(Schema $schema): Schema
    {
        $schema = parent::_buildSchema($schema);
        $schema->addField('name', [
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
        $validator = parent::validationDefault($validator);
        $validator->notEmptyString('name');
        return $validator;
    }

    /**
     * Defines what to execute once the Form is processed
     *
     * @param array $data Form data.
     * @return bool
     * @throws \Exception
     */
    protected function _execute(array $data): bool
    {
        return $this->getMediaManager($data['config'])
            ->createDirectory($data['path'] . '/' . $data['name']);
    }
}
