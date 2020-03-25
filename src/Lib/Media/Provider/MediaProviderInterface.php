<?php
declare(strict_types=1);

/**
 * Created by PhpStorm.
 * User: flow
 * Date: 9/5/15
 * Time: 5:23 PM
 */

namespace Media\Lib\Media\Provider;

interface MediaProviderInterface
{
    public function __construct(array $config);

    /**
     * Read contents of directory path
     * @param $path string Path to directory
     * @return array List of files and directories
     */
    public function read($path);
}
