<?php
/**
 * @var string $path
 * @var \Cake\Filesystem\Folder $selectedDir
 * @var \Media\Form\NewFolderForm $form
 */
?>
<div class="view">
    <?php
    echo $this->Form->create($form);
    echo $this->Form->input('path', ['disabled' => false, 'read-only' => true, 'value' => $path]);
    echo $this->Form->input('name');
    echo $this->Form->submit(__d('media', 'Create folder'));
    ?>
</div>