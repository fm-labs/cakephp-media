<?php

use Cake\Core\Configure;
use Cake\Core\Plugin;
use Cake\Database\Type;
use Cake\Event\EventManager;

/**
 * Load Media plugin configuration
 */
Configure::load('Media.media');
try { Configure::load('media'); } catch (\Exception $ex) {}
try { Configure::load('local/media'); } catch (\Exception $ex) {}

// Register MediaFileType
Type::map('media_file', 'Media\Database\Type\MediaFileType');


// Banana Hook
if (Plugin::loaded('Backend')) {

    \Backend\View\Helper\FormatterHelper::register('media_file', function($val, $extra, $params) {
        return h($val);
    });

    \Backend\View\Helper\FormatterHelper::register('media_files', function($val, $extra, $params) {
        return h($val);
    });

    EventManager::instance()->on(new \Media\Backend\MediaBackend());
}

//$listener = new LocalFileStorageListener();
//EventManager::instance()->on($listener);

// For automated image processing you'll have to attach this listener as well
//$listener = new ImageProcessingListener();
//EventManager::instance()->on($listener);
