<?php

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
