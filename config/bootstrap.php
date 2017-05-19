<?php

use Cake\Core\Configure;
use Cake\Database\Type;

/**
 * Load Media plugin configuration
 */
Configure::load('Media.media');

// Register MediaFileType
Type::map('media_file', 'Media\Database\Type\MediaFileType');



# Manually initialize MediaPlugin
# Note: Configurations have to be loaded manually as well!
//Configure::load('media');
//$MediaPlugin = new \Media\MediaPlugin();
//Cake\Event\EventManager::instance()->on($MediaPlugin);
//$MediaPlugin();
