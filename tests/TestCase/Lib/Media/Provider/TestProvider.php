<?php
declare(strict_types=1);

namespace Media\Test\TestCase\Lib\Media\Provider;

use Media\Lib\Media\Provider\MediaProviderInterface;

class TestProvider implements MediaProviderInterface
{
    public function baseUrl()
    {
    }

    public function connect()
    {
    }

    public function disconnect()
    {
    }

    public function listFiles($path)
    {
    }

    public function listFilesRecursive($path)
    {
    }

    public function listFolders($path)
    {
    }

    public function listFoldersRecursive($path, $depth = -1)
    {
    }

    public function readFile($path)
    {
    }

    public function basePath()
    {
        // TODO: Implement basePath() method.
    }

    public function __construct(array $config)
    {
    }

    /**
     * Read contents of directory path
     * @param $path string Path to directory
     * @return array List of files and directories
     */
    public function read($path)
    {
    }
}
