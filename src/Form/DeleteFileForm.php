<?php
declare(strict_types=1);

namespace Media\Form;


/**
 * NewFolder Form.
 */
class DeleteFileForm extends FileForm
{
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
            ->deleteFile($data['path'] . '/' . $data['file']);
    }
}
