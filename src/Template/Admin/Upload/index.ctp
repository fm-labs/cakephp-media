<?php
$this->assign('title', __d('media','Upload'))
?>
<div class="index">
    <?php
    echo $this->Form->create(null, ['type' => 'file']);
    echo $this->Form->control('upload', ['type' => 'file']);
    echo $this->Form->submit(__d('media','Upload file'));
    ?>
</div>