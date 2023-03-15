<?php
declare(strict_types=1);

namespace Media\Filesystem;

use League\Flysystem\Filesystem as Flysystem;
use League\Flysystem\Local\LocalFilesystemAdapter;

class LeagueFilesystem extends Flysystem implements FilesystemInterface
{
    /**
     * Construct a new Flysystem filesystem
     */
    public function __construct()
    {
        $adapter = new LocalFilesystemAdapter(WWW_ROOT);
        parent::__construct($adapter);
    }
}
