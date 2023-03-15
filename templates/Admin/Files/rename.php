<?php
/**
 * @var string $config
 * @var string $path
 * @var string $file
 * @var \Media\Form\RenameFileForm $form
 */

$this->extend('index');
$this->assign('title', __d('media', 'Rename File'))
?>
<div class="view">
    <?php
    echo $this->Form->create($form);
    echo $this->Form->hidden('config', ['disabled' => false, 'readonly' => true, 'value' => $config]);
    echo $this->Form->hidden('path', ['disabled' => false, 'readonly' => true, 'value' => $path]);
    echo $this->Form->input('file', ['disabled' => false, 'readonly' => true, 'value' => $file]);
    echo $this->Form->input('new_name', ['default' => $file]);
    echo $this->Form->submit(__d('media', 'Rename file'));
    ?>
</div>
