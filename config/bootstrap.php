<?php

use Cake\Core\Configure;
use Cake\Database\Type;
use Cake\Log\Log;

/**
 * Load Media plugin configuration
 */
Configure::load('Media.media');

// Register MediaFileType
Type::map('media_file', 'Media\Database\Type\MediaFileType');

if (!Log::config('media')) {
    Log::config('media', [
        'className' => 'Cake\Log\Engine\FileLog',
        'path' => LOGS,
        'file' => 'media',
        'scopes' => ['media']
    ]);
}