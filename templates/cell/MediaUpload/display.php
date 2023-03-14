<?php
use Cake\Core\Configure;

/** @var array $upload */
$upload = $this->get('upload');
/** @var array $uploadConfig */
$uploadConfig = $this->get('uploadConfig', []);
/** @var string $uploadPath */
$uploadPath = $this->get('uploadPath');
/** @var bool $uploadMultiple */
$uploadMultiple = $this->get('uploadMultiple');
?>
<div class="media-uploader">

    <?php if (isset($error)) : ?>
        <div class="alert alert-danger"><?= h($error); ?></div>
    <?php endif; ?>

    <?php if (isset($uploadForm)) : ?>
    <div class="upload-form form">
        <div class="box box-default box-solid with-border">
            <div class="box-header">
                <?= __d('media', 'Upload file'); ?>
                <small>to <?= $uploadPath; ?></small>
            </div>
            <div class="box-body p-2">
                <?php if (isset($upload) && $upload) : ?>
                    <?php if (isset($upload['upload_err'])) : ?>
                        <div class="alert alert-danger">
                            <?= h($upload['upload_err']); ?>
                        </div>
                    <?php else : ?>
                        <div class="alert alert-success">
                            <?= __d('media', 'Uploaded: {0}', $upload['name']); ?>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
                <?php
                //            echo $this->Form->create(null, ['type' => 'file']);
                //            echo $this->Form->control('config', ['type' => 'text', 'value' => $this->get('config')]);
                //            echo $this->Form->control('upload_file', ['type' => 'file']);
                echo $this->Form->create($this->get('uploadForm'), [
                    'type' => 'file',
                ]);
                if (Configure::read('Media.debug')) {
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
                    echo $this->Form->control('upload_file', [
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
    <?php endif; ?>
</div>