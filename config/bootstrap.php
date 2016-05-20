<?php

use Backend\Lib\Backend;
use Cake\Core\Configure;
use Cake\Core\Plugin;
use Cake\Database\Type;
use Cake\Event\EventManager;
use Burzum\FileStorage\Lib\FileStorageUtils;
use Burzum\FileStorage\Lib\StorageManager;
use Burzum\FileStorage\Event\ImageProcessingListener;
use Burzum\FileStorage\Event\LocalFileStorageListener;

// Check Media configuration
if (!Configure::read('Media')) {
    die("Media Plugin not configured");
}

// Banana Hook
if (Plugin::loaded('Backend')) {
    Backend::hookPlugin('Media');
}

// Register MediaFileType
Type::map('media_file', 'Media\Database\Type\MediaFileType');

//$listener = new LocalFileStorageListener();
//EventManager::instance()->on($listener);

// For automated image processing you'll have to attach this listener as well
//$listener = new ImageProcessingListener();
//EventManager::instance()->on($listener);