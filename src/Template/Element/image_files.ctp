<?php
if (!isset($images)) $images = [];
if (!isset($imageOptions)) $imageOptions = [];
if (!isset($imageActions)) $imageActions = [];
if (!isset($actions)) $actions = [];
?>
<div class="media select file images">
<?php if (isset($label)): ?>
    <label><?= $label ;?></label>
<?php endif; ?>
<?php
if (!empty($images)) {
    foreach ($images as $imageFile) {
        echo $this->element('Media.image_file', [
            'label' => false,
            'image' => $imageFile,
            'imageOptions' => $imageOptions,
            'imageActions' => $imageActions,
        ]);
    }
}
?>
<div class="actions">
    <?php
    foreach ($actions as $action) {
        echo $this->Html->link($action[0], $action[1], $action[2]);
    }
    ?>
</div>
</div>
