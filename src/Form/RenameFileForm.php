<?php
declare(strict_types=1);

namespace Media\Form;


use Cake\Form\Schema;
use Cake\Validation\Validator;

/**
 * RenameFileForm.
 */
class RenameFileForm extends FileForm
{
    public function _buildSchema(Schema $schema): Schema
    {
        $schema = parent::_buildSchema($schema);
        $schema->addField('new_name', [
            'required' => true
        ]);
        return $schema;
    }

    public function validationDefault(Validator $validator): Validator
    {
        $validator = parent::validationDefault($validator);
        $validator->notEmptyString('new_name');
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
        $oldFilepath = $data['path'] . $data['file'];
        $newFilePath = $data['path'] . $data['new_name'];

        return $this->getMediaManager($data['config'])
            ->renameFile($oldFilepath, $newFilePath);
    }
}
