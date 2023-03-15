<?php

namespace Media\Filesystem;

use DateTimeInterface;
use League\Flysystem\DirectoryListing;

/**
 * @method string publicUrl(string $path, array $config = [])
 * @method string temporaryUrl(string $path, DateTimeInterface $expiresAt, array $config = [])
 * @method string checksum(string $path, array $config = [])
 */
class CakeFilesystem implements FilesystemInterface
{
    public function __construct()
    {
    }

    /**
     * @inheritDoc
     */
    public function fileExists(string $location): bool
    {
        return file_exists($location);
    }

    /**
     * @inheritDoc
     */
    public function directoryExists(string $location): bool
    {
        return is_dir($location);
    }

    /**
     * @inheritDoc
     */
    public function has(string $location): bool
    {
        return file_exists($location);
    }

    /**
     * @inheritDoc
     */
    public function read(string $location): string
    {
        return file_get_contents($location);
    }

    /**
     * @inheritDoc
     */
    public function readStream(string $location)
    {
        throw new \BadMethodCallException("Not implemented");
    }

    /**
     * @inheritDoc
     */
    public function listContents(string $location, bool $deep = self::LIST_SHALLOW): DirectoryListing
    {
        return new DirectoryListing();
    }

    /**
     * @inheritDoc
     */
    public function lastModified(string $path): int
    {
        // TODO: Implement lastModified() method.
        return time();
    }

    /**
     * @inheritDoc
     */
    public function fileSize(string $path): int
    {
        // TODO: Implement fileSize() method.
        return 0;
    }

    /**
     * @inheritDoc
     */
    public function mimeType(string $path): string
    {
        // TODO: Implement mimeType() method.
        return "application/octet-stream";
    }

    /**
     * @inheritDoc
     */
    public function visibility(string $path): string
    {
        // TODO: Implement visibility() method.
        return "0777";
    }

    /**
     * @inheritDoc
     */
    public function write(string $location, string $contents, array $config = []): void
    {
        // TODO: Implement write() method.
    }

    /**
     * @inheritDoc
     */
    public function writeStream(string $location, $contents, array $config = []): void
    {
        // TODO: Implement writeStream() method.
    }

    /**
     * @inheritDoc
     */
    public function setVisibility(string $path, string $visibility): void
    {
        // TODO: Implement setVisibility() method.
    }

    /**
     * @inheritDoc
     */
    public function delete(string $location): void
    {
        unlink($location);
    }

    /**
     * @inheritDoc
     */
    public function deleteDirectory(string $location): void
    {
        // TODO: Implement deleteDirectory() method.
        unlink($location);
    }

    /**
     * @inheritDoc
     */
    public function createDirectory(string $location, array $config = []): void
    {
        // TODO: Implement createDirectory() method.
        mkdir($location);
    }

    /**
     * @inheritDoc
     */
    public function move(string $source, string $destination, array $config = []): void
    {
        // TODO: Implement move() method.
    }

    /**
     * @inheritDoc
     */
    public function copy(string $source, string $destination, array $config = []): void
    {
        // TODO: Implement copy() method.
    }

    public function __call(string $name, array $arguments)
    {
        // TODO: Implement @method string publicUrl(string $path, array $config = [])
        // TODO: Implement @method string temporaryUrl(string $path, DateTimeInterface $expiresAt, array $config = [])
        // TODO: Implement @method string checksum(string $path, array $config = [])
    }
}