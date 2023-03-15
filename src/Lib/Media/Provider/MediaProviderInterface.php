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
     * @throws \Exception
     */
    public function read(string $path): array;

    /**
     * @param string $path
     * @return bool
     * @throws \Exception
     */
    public function createDirectory(string $path): bool;

    /**
     * @param string $path
     * @return bool
     * @throws \Exception
     */
    public function directoryExists(string $path): bool;

    /**
     * @param string $path
     * @return bool
     * @throws \Exception
     */
    public function fileExists(string $path): bool;

    /**
     * @param string $path
     * @return bool
     * @throws \Exception
     */
    public function writeFile(string $path, string $contents): bool;

    /**
     * @param string $path
     * @return bool
     * @throws \Exception
     */
    public function deleteDirectory(string $path): bool;

    /**
     * @param string $path
     * @return bool
     * @throws \Exception
     */
    public function deleteFile(string $path): bool;

    /**
     * @param string $source
     * @param string $destination
     * @return bool
     * @throws \Exception
     */
    public function move(string $source, string $destination): bool;
}
