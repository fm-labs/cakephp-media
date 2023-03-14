<?php

namespace Media\Filesystem;

use Cake\Core\Configure;
use DateTimeInterface;
use League\Flysystem\DirectoryListing;
use Media\Lib\Media\Provider\LocalStorageProvider;

/**
 * @method string publicUrl(string $path, array $config = [])
 * @method string temporaryUrl(string $path, DateTimeInterface $expiresAt, array $config = [])
 * @method string checksum(string $path, array $config = [])
 */
class CakeFilesystem implements FilesystemInterface
{
    public function __construct()
    {
        $adapter = new LocalStorageProvider(Configure::read('Media.default'));
    }

    /**
     * @inheritDoc
     */
    public function fileExists(string $location): bool
    {
        // TODO: Implement fileExists() method.
    }

    /**
     * @inheritDoc
     */
    public function directoryExists(string $location): bool
    {
        // TODO: Implement directoryExists() method.
    }

    /**
     * @inheritDoc
     */
    public function has(string $location): bool
    {
        // TODO: Implement has() method.
    }

    /**
     * @inheritDoc
     */
    public function read(string $location): string
    {
        // TODO: Implement read() method.
    }

    /**
     * @inheritDoc
     */
    public function readStream(string $location)
    {
        // TODO: Implement readStream() method.
    }

    /**
     * @inheritDoc
     */
    public function listContents(string $location, bool $deep = self::LIST_SHALLOW): DirectoryListing
    {
        // TODO: Implement listContents() method.
    }

    /**
     * @inheritDoc
     */
    public function lastModified(string $path): int
    {
        // TODO: Implement lastModified() method.
    }

    /**
     * @inheritDoc
     */
    public function fileSize(string $path): int
    {
        // TODO: Implement fileSize() method.
    }

    /**
     * @inheritDoc
     */
    public function mimeType(string $path): string
    {
        // TODO: Implement mimeType() method.
    }

    /**
     * @inheritDoc
     */
    public function visibility(string $path): string
    {
        // TODO: Implement visibility() method.
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
        // TODO: Implement delete() method.
    }

    /**
     * @inheritDoc
     */
    public function deleteDirectory(string $location): void
    {
        // TODO: Implement deleteDirectory() method.
    }

    /**
     * @inheritDoc
     */
    public function createDirectory(string $location, array $config = []): void
    {
        // TODO: Implement createDirectory() method.
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