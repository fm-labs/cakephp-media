<?php

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
}