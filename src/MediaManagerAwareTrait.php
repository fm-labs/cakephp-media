<?php

namespace Media;

trait MediaManagerAwareTrait
{
    protected ?MediaManager $_manager = null;

    /**
     * @return \Media\MediaManager
     * @throws \Exception
     */
    public function getMediaManager(string $config): MediaManager
    {
        if (!$this->_manager /*|| $this->_manager->getConfigName() !== $config*/) {
            $this->_manager = MediaManager::get($config);
        }

        return $this->_manager;
    }
}