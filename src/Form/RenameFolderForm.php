<?php
declare(strict_types=1);

namespace Media\Form;


use Cake\Form\Schema;
use Cake\Validation\Validator;

/**
 * RenameFolderForm.
 */
class RenameFolderForm extends FolderForm
{
    public function _buildSchema(Schema $schema): Schema
    {
        $schema = parent::_buildSchema($schema);
        $schema->addField('new_path', [
            'required' => true
        ]);
        return $schema;
    }

    public function validationDefault(Validator $validator): Validator
    {
        $validator = parent::validationDefault($validator);
        $validator->notEmptyString('new_path');
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
            ->renameDirectory($data['path'], $data['new_path']);
    }
}
