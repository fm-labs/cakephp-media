<?php
/**
 * @var \Cake\Filesystem\File $selectedFile
 */
?>
<div class="view">
    <?php
    echo $this->Form->create();
    echo $this->Form->input('path', ['disabled' => false, 'read-only' => true, 'value' => $selectedFile->path]);
    echo $this->Form->input('oldName', ['disabled' => true, 'read-only' => true, 'value' => $selectedFile->name()]);
    echo $this->Form->input('newName');
    echo $this->Form->submit(__d('media', 'Rename'));
    ?>
</div>