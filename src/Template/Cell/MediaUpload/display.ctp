<div class="media-uploader">

    <?php if (isset($error)): ?>
    <div class="alert alert-danger"><?= h($error); ?></div>
    <?php endif; ?>

    <?php if (isset($uploadForm)): ?>
    <div class="upload-form form">
        <div class="box box-default box-solid with-border">
            <div class="box-header">
                <?= __d('media', 'Upload file'); ?>
                <small>to <?= $uploadPath; ?></small>
            </div>
            <div class="box-body">
                <?php if (isset($upload)): ?>
                    <?php if (isset($upload['upload_err'])): ?>
                        <div class="alert alert-danger">
                            <?= h($upload['upload_err']); ?>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-success">
                            <?= __('Uploaded: {0}', $upload['name']); ?>
                        </div>
                    <?php endif; ?>
                    <?php debug($upload); ?>
                <?php endif; ?>
                <?php
                //            echo $this->Form->create(null, ['type' => 'file']);
                //            echo $this->Form->input('config', ['type' => 'text', 'value' => $this->get('config')]);
                //            echo $this->Form->input('upload_file', ['type' => 'file']);
                echo $this->Form->create($this->get('uploadForm'), [
                    'type' => 'file'
                ]);
                if ($uploadMultiple) {
                    echo $this->Form->input('upload_file', [
                        'label' => false, //__d('media', 'Select files'),
                        'type' => 'file',
                        'multiple' => 'multiple'
                    ]);
                } else {
                    echo $this->Form->input('upload_file', [
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