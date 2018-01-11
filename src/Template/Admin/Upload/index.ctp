<?php
$this->assign('title', __('Upload'))
?>
<div class="index">
    <?php
    echo $this->Form->create(null, ['type' => 'file']);
    echo $this->Form->input('upload', ['type' => 'file']);
    echo $this->Form->submit(__('Upload file'));
    ?>
</div>