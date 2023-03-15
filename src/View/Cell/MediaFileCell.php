<?php
declare(strict_types=1);

namespace Media\View\Cell;

use Cake\View\Cell;
use Media\Model\Entity\MediaFile;

/**
 * MediaFile cell
 */
class MediaFileCell extends Cell
{
    /**
     * List of valid options that can be passed into this
     * cell's constructor.
     *
     * @var array<string, mixed>
     */
    protected $_validCellOptions = [];

    /**
     * Initialization logic run at the end of object construction.
     *
     * @return void
     */
    public function initialize(): void
    {
    }

    /**
     * Default display method.
     *
     * @return void
     */
    public function display(?MediaFile $mediaFile = null)
    {
        $mediaFile = $mediaFile ?: $this->getMediaFileFromContext();
        $this->set('mediaFile', $mediaFile);
    }

    protected function getMediaFileFromContext(): ?MediaFile
    {
        $config = $this->request->getParam('config');
        $path = $this->request->getQuery('path');
        $file = $this->request->getQuery('file');

//        debug($config);
//        debug($path);
//        debug($file);
        if ($config && $path && $file) {
            return MediaFile::fromPath($path . $file, $config);
        }
        return null;
    }
}
