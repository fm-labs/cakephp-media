<?php

namespace Media\Model\Entity;

use Cake\Collection\Collection;

class MediaFileCollection extends Collection
{
    public function __toString()
    {
        $paths = [];

        foreach ($this as $item) {
            $paths[] = $item->path;
        }

        return join(',', $paths);
    }

    public function getPaths()
    {
        $paths = [];

        foreach ($this as $item) {
            $paths[] = $item->path;
        }

        return $paths;
    }
}
