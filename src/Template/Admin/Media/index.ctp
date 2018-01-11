<?php //$this->Html->script('Backend./libs/underscore/underscore-min.js', ['block' => true]); ?>
<?php //$this->Html->script('Backend./libs/backbone/backbone-min.js', ['block' => true]); ?>
<?php $this->Html->script('Media.media.js', ['block' => true]); ?>
<?php $dataUrl = $this->Html->Url->build(['action' => 'data', '_ext' => 'json'], true); ?>
<div class="index">

    <div id="media-container" class="media-container" data-media-url="<?= $dataUrl ?>" data-media-baseurl="<?= $this->Html->Url->build(MEDIA_URL); ?>">
        <!-- Show media browser here -->
    </div>

</div>
<?php $this->append('script'); ?>
<script>
    $('#media-container').CakeMedia();
</script>
<?php $this->end(); ?>
<?php debug($dataUrl); ?>
