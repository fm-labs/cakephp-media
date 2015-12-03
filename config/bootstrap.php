<?php
Cake\Core\Configure::load('media');

\Cake\Database\Type::map('media_file', 'Media\Database\Type\MediaFileType');