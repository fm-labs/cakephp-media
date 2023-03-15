<?php
/**
 * @var string $config
 * @var string $path
 * @var \Media\Form\MediaUploadForm $form
 */

use Cake\Core\Configure;

$this->extend('index');
$this->assign('title', __d('media', 'Upload'));

/** @var array $upload */
$upload = $this->get('upload');
/** @var array $uploadConfig */
$uploadConfig = $this->get('uploadConfig', []);
/** @var bool $uploadMultiple */
$uploadMultiple = $this->get('uploadMultiple', false);
?>
<div class="view media-uploader">

    <div class="upload-form form">
        <div class="box box-default box-solid with-border">
            <div class="box-header">
                <?= __d('media', 'Upload file'); ?>
                <small>to <?= $path; ?></small>
            </div>
            <div class="box-body p-2">
                <?php
                echo $this->Form->create($form, [
                    'type' => 'file',
                ]);
                echo $this->Form->hidden('config', ['disabled' => false, 'readonly' => true, 'value' => $config]);
                echo $this->Form->hidden('path', ['disabled' => false, 'readonly' => true, 'value' => $path]);

                if (Configure::read('Media.debug'))
                {
                    $infoTemplate = '<div><strong>%s:</strong>&nbsp;%s</div>';
                    echo sprintf($infoTemplate, 'Upload to', $uploadConfig['uploadDir'] ?? '?');
                    echo sprintf($infoTemplate, 'Max file size', $uploadConfig['maxFileSize'] ?? '?');
                    echo sprintf($infoTemplate, 'Allowed mime types', $uploadConfig['mimeTypes'] ?? '?');
                    echo sprintf($infoTemplate, 'Allowed file extensions', $uploadConfig['fileExtensions'] ?? '*');
                    echo sprintf($infoTemplate, 'Multiple', $uploadConfig['multiple'] ? 'Yes' : 'No');
                    echo sprintf($infoTemplate, 'Overwrite', $uploadConfig['overwrite'] ? 'Yes' : 'No');
                    echo sprintf($infoTemplate, 'hashFilename', $uploadConfig['hashFilename'] ? 'Yes' : 'No');
                    echo sprintf($infoTemplate, 'uniqueFilename', $uploadConfig['uniqueFilename'] ? 'Yes' : 'No');
                }

                if ($uploadMultiple) {
                    echo $this->Form->control('upload_file[]', [
                        'label' => false, //__d('media', 'Select files'),
                        'type' => 'file',
                        'multiple' => 'multiple',
                    ]);
                } else {
                    echo $this->Form->control('upload_file', [
                        'label' => false, //__d('media', 'Select file'),
                        'type' => 'file',
                    ]);
                }
                echo $this->Form->submit(__d('media', 'Upload'));
                ?>
            </div>
        </div>
    </div>
</div>