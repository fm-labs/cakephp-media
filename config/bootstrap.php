<?php

use Cake\Core\Configure;
use Cake\Core\Plugin;
use Cake\Database\Type;
use Cake\Event\EventManager;

// Check Media configuration
if (!Configure::read('Media')) {
    die("Media Plugin not configured");
}

// Banana Hook
if (Plugin::loaded('Backend')) {
    \Backend\Lib\Backend::hookPlugin('Media');

    \Backend\View\Helper\FormatterHelper::register('media_file', function($val, $extra, $params) {
        return h($val);
    });

    \Backend\View\Helper\FormatterHelper::register('media_files', function($val, $extra, $params) {
        return h($val);
    });
}


// Register MediaFileType
Type::map('media_file', 'Media\Database\Type\MediaFileType');

//$listener = new LocalFileStorageListener();
//EventManager::instance()->on($listener);

// For automated image processing you'll have to attach this listener as well
//$listener = new ImageProcessingListener();
//EventManager::instance()->on($listener);