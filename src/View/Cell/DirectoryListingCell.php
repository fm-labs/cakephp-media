<?php
declare(strict_types=1);

namespace Media\View\Cell;

use Cake\View\Cell;
use Media\MediaManagerAwareTrait;

/**
 * DirectoryListing cell
 */
class DirectoryListingCell extends Cell
{
    use MediaManagerAwareTrait;

    /**
     * List of valid options that can be passed into this
     * cell's constructor.
     *
     * @var array<string, mixed>
     */
    protected $_validCellOptions = ['mediaConfig'];

    protected ?string $mediaConfig = null;

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
    public function display(?string $path = null, ?string $file = null)
    {
        $folders = $files = [];
        try {
            $manager = $this->getMediaManager($this->mediaConfig);
            $contents = $manager->read($path);
            [$folders, $files] = $contents;

//            if ($file) {
//                $mf = MediaFile::fromPath($path . $file, $this->config);
//                $this->set('selectedFile', $mf);
//            }
        } catch (\Exception $ex) {
            debug($ex->getMessage());
        }

        $this->set('config', $this->mediaConfig);
        $this->set('path', $path);
        $this->set('file', $file);
        $this->set('files', $files);
        $this->set('folders', $folders);
    }
}
