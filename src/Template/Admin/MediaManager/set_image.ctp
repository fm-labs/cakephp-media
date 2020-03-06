<?= $this->Html->script('Backend.underscore-min', ['block' => true]); ?>
<?= $this->Html->script('Backend.backbone-min', ['block' => true]); ?>
<?= $this->Html->css('Backend.jstree/themes/backend/style.min', ['block' => true]); ?>
<?= $this->Html->script('Backend.jstree/jstree.min', ['block' => true]); ?>
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
                <div id="is-path" class="panel-heading">Select Folder</div>
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
                'empty' => __d('banana','- Choose Image -'),
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
<script>

    var debug = false;

    function log(msg)
    {
        console.log(msg);
        $('#is-log').prepend($('<div>').html(msg));
    }

    $(document).on('submit', '#imageselect-form', function(e) {
        e.preventDefault();

        var data = $(this).serialize();
        var action = $(this).attr('action');
        var method = $(this).attr('method') || 'POST';
        var _self = $(this);

        alert("Submit form to " + action + " via " + method + " with data " + data);
    });

    $(document).on('mouseover', '.thumbnails li .thumbnail', function() {
    });

    $(document).ready(function() {

        if (!$.fn.imagepicker) {
            console.warn("Image Picker not loaded");
        }

        var $isContainer = $('#is-container');
        var $isPath = $('#is-path');
        var $isTree = $('#is-tree');
        var $isFiles = $('#is-files');
        var $isLog = $('#is-log');

        var $isSelected =  $('#is-selected');
        var $isSelect = $('#imagepicker-select');
        var dataUrl = $isContainer.data('url');


        $isSelect.on('change', function() {

            $isSelected.html("");

            var val = $(this).find(":selected").val();
            var imgUrl = $(this).find(":selected").data('imgSrc');

            console.log("Sel: " + val + ":" + imgUrl);

            $('<img>', { class: 'selected', src: imgUrl, title: val})
                .appendTo($isSelected);

        }).trigger('change');



        $isTree
            .on('changed.jstree', function (e, data) {
                var i, j, r = [];
                console.log(data);
                if (data.action === "select_node") {
                    for(i = 0, j = data.selected.length; i < j; i++) {
                        r.push(data.instance.get_node(data.selected[i]).id);
                    }
                    //$('.filepicker .folder-selected').html('Selected: ' + r.join(', '));
                    log('Selected: ' + r.join(', '));

                    path = r.join('/');
                    log('Path: ' + path);

                    var config = 'images';
                    var url = $isContainer.data('filesUrl') + '&id='+path;

                    $.ajax({
                        method: 'GET',
                        url: url,
                        dataType: 'json',
                        data: {'selected': r },
                        beforeSend: function() {

                        },
                        success: function(data) {

                            console.log(data);

                            $isPath.html("<i class=\"ui green outline folder icon\" />&nbsp;" + path);

                            // no files in folder
                            if (data.length === 0) {
                                $isFiles.html('<div class="ui info message"><i class="info icon"></i>No files in folder ' + path + '</div>');
                                return;
                            }

                            $isFiles.html("");


                            var $isFilesSelect = $('<select>', { class: 'image-picker'});
                            //$isSelect.append('<option value="" data-img-src="data:image/png;base64,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">No Image</option>');
                            $isFilesSelect.append('<option value=""></option>')

                            for(var i in data) {
                                var file = data[i];
                                $('<option>', { 'data-img-src': file.icon, value: file.id }).html(file.text).appendTo($isFilesSelect);
                            }

                            $isFilesSelect.appendTo($isFiles);
                            $isFilesSelect.imagepicker({
                                hide_select: !debug,
                                show_label: true,
                                changed: function(old, values) {
                                    console.log(values);
                                    if (values[0] !== undefined) {
                                        $isSelect.val(values[0]).trigger('change');
                                    }
                                }
                            });
                        }
                    });

                }

            })
            .jstree({
                "core" : {
                    "themes" : {
                        //"variant" : "large"
                    },
                    'data' : {
                        'url': function (node) {
                            console.log(node);
                            return $isContainer.data('treeUrl');
                        },
                        'data': function (node) {
                            console.log(node)
                            return {'id': node.id};
                        },
                    }
                },
                "checkbox" : {
                    "keep_selected_style" : false
                },
                "plugins" : [ "wholerow", "changed", "state" ] // , "checkbox"
            });
    });
</script>
