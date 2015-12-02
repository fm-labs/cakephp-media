<?php
/**
 * Created by PhpStorm.
 * User: flow
 * Date: 9/5/15
 * Time: 5:23 PM
 */

namespace Media\Lib\Media\Provider;

interface MediaProviderInterface
{
    public function baseUrl();

    public function connect();

    public function disconnect();

    public function listFiles($path);

    public function listFilesRecursive($path);

    public function listFolders($path);

    public function listFoldersRecursive($path);

    public function readFile($path);
}
