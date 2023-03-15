<?php
/**
 * @var string $config
 * @var string $path
 * @var \Media\Form\RenameFileForm $form
 */

$this->extend('index');
$this->assign('title', __d('media', 'Rename folder'))
?>
<div class="view">
    <?php
    echo $this->Form->create($form);
    echo $this->Form->hidden('config', ['disabled' => false, 'readonly' => true, 'value' => $config]);
    echo $this->Form->hidden('path', ['disabled' => false, 'readonly' => true, 'value' => $path]);
    echo $this->Form->input('new_path', ['default' => $path]);
    echo $this->Form->submit(__d('media', 'Rename file'));
    ?>
</div>
