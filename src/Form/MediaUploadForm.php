<?php
declare(strict_types=1);

namespace Media\Form;

use Upload\Form\UploadForm;
use Upload\Uploader;

/**
 * MediaUploadForm
 */
class MediaUploadForm extends UploadForm
{
    /**
     * @var string Name of media config
     */
    protected string $mediaConfig;

    /**
     * @param array|string $mediaConfig
     * @param \Upload\Uploader|array|string $uploaderConfig
     * @throws \Exception
     */
    public function __construct(array|string $mediaConfig = 'default', array|string|Uploader $uploaderConfig = [])
    {
        $this->mediaConfig = $mediaConfig;
        parent::__construct($uploaderConfig);
    }

    /**
     * @param array $data
     * @return bool
     * @throws \Exception
     */
    protected function _execute(array $data): bool
    {
        return parent::_execute($data);
    }
}
