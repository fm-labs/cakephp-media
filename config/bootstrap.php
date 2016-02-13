<?php

use Backend\Lib\Backend;
use Cake\Core\Configure;
use Cake\Core\Plugin;
use Cake\Database\Type;

if (!Configure::read('Media')) {
    die("Media Plugin not configured");
}

Type::map('media_file', 'Media\Database\Type\MediaFileType');

if (Plugin::loaded('Backend')) {
    Backend::hookPlugin('Media');
}