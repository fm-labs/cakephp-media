<?php
declare(strict_types=1);

namespace Media\Model\Entity;

interface MediaFileInterface
{
    public function getPath();

    public function getUrl();

    /**
     * @return bool
     */
    public function isImage();
}
