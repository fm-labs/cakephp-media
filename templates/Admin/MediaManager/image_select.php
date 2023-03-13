<?= $this->Html->script('Admin.jstree/jstree.min', ['block' => true]); ?>
<style>
    #is-selected img {
        max-width: 100%;
        max-height: 100px;
    }
</style>
<div id="is-container"
     data-tree-url="<?= $this->Url->build(['action' => 'treeData', 'config' => $config, '_ext' => 'json']); ?>"
     data-files-url="<?= $this->Url->build(['action' => 'filesData', 'config' => $config, '_ext' => 'json']); ?>">

    <div class="row">
        <div class="col-sm-3">
            <div id="is-tree">Loading ...</div>
        </div>
        <div class="col-sm-6">
            <div class="panel panel-default">
                <div id="is-path" class="panel-heading">No folder selected</div>
                <div id="is-files" class="panel-body"></div>
            </div>
        </div>
        <div class="col-sm-3">

            <?= $this->Form->create($content, ['_url' => [
                'action' => 'setImage',
                'scope' => $scope,
                'multiple' => $multiple,
                'model' => $model,
                'id' => $id,
                'config' => $config
            ]]); ?>
            <?php
            echo $this->Form->control($scope, [
                'type' => 'imageselect',
                'multiple' => $multiple,
                'options' => $imageFiles,
                'class' => 'grouped',
                'id' => 'imagepicker-select',
                'empty' => __d('media','- Choose Image -'),
                'hidden' => true,
                //'style' => 'min-height: 500px;'
            ]); ?>

            <?= $this->Form->submit('Save', ['class' => 'btn btn-primary btn-block']); ?>
            <?= $this->Form->end(); ?>

            <h4>Selected</h4>
            <div id="is-selected"></div>
        </div>
    </div>
</div>