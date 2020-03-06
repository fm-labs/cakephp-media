<?php //$this->Html->script('Backend./libs/underscore/underscore-min.js', ['block' => true]); ?>
<?php //$this->Html->script('Backend./libs/backbone/backbone-min.js', ['block' => true]); ?>
<?php $this->Html->script('Media.media.js', ['block' => true]); ?>
<?php $dataUrl = $this->Html->Url->build(['action' => 'data', '_ext' => 'json'], true); ?>
<div class="index">

    <div id="media-container" class="media-container" data-media-url="<?= $dataUrl ?>" data-media-baseurl="<?= $this->Html->Url->build(MEDIA_URL); ?>">
        <!-- Show media browser here -->
    </div>

    <div id="media-upload">
        <?php
        $this->assign('title', __d('media','Upload'))
        ?>
        <div class="upload-form form">
            <?php
//            echo $this->Form->create(null, ['type' => 'file']);
//            echo $this->Form->control('config', ['type' => 'text', 'value' => $this->get('config')]);
//            echo $this->Form->control('upload_file', ['type' => 'file']);
            echo $this->Form->create($uploadForm, ['type' => 'file']);
            echo $this->Form->control('config');
            echo $this->Form->control('upload_file', ['type' => 'file']);
            echo $this->Form->submit(__d('media','Upload file'));
            ?>
        </div>
    </div>

</div>
<?php $this->append('script'); ?>
<script>
    $('#media-container').CakeMedia();
</script>
<?php $this->end(); ?>
<?php debug($dataUrl); ?>
