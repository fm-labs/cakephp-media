<?php

namespace Media\Lib\FileManager\Provider;

interface MediaProviderInterface
{
    /* Connection */

    /**
     * Connect
     */
    public function connect();

    /**
     * Disconnect
     */
    public function disconnect();

    /* File Listing */

    /**
     * Read contents of directory path
     * @param $path string Path to directory
     * @return array List of files and directories
     */
    public function read($path);
}
