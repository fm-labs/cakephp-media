<?php

use Cake\Core\Configure;
use Cake\Database\Type;

if (!Configure::read('Media')) {
    die("Media Plugin not configured");
}


Type::map('media_file', 'Media\Database\Type\MediaFileType');
