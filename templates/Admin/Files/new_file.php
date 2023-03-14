<?php
/**
 * @var \Cake\Filesystem\Folder $selectedFile
 */

/** @var \Media\Form\NewFileForm $form */
$form = $this->get('form')
?>
<div class="view">
    <?php
    echo $this->Form->create($form);
    echo $this->Form->input('path', ['disabled' => false, 'read-only' => true, 'value' => $selectedFile->path]);
    echo $this->Form->input('name');
    echo $this->Form->submit(__d('media', 'Create file'));
    ?>
</div>