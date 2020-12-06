<?php
declare(strict_types=1);

namespace Media\Model\Entity;

interface MediaFileInterface
{
    /**
     * @return string
     */
    public function getPath(): string;

    /**
     * @return string
     */
    public function getUrl(): string;

    /**
     * @return bool
     */
    public function isImage(): bool;
}
