(function ( $ ) {

    var MediaPicker = (function() {
        function MediaPicker() {
        }
    })();

    $.fn.mediapicker = function( options ) {

        // This is the easiest way to have default options.
        var settings = $.extend({
            multiple: false,
            modal: false,
            preview: true,
            treeUrl: null,
            filesUrl: null,
            textSelect: 'Select', // @deprecated
            textRemove: 'Remove' // @deprecated
        }, options );

        console.log("[mediapicker] Settings", settings);

        var pickerCount = 0;

        if (!settings.treeUrl) {
            console.warn("[mediapicker] TreeUrl not defined");
        }
        if (!settings.filesUrl) {
            console.warn("[mediapicker] FilesUrl not defined");
        }

        return this.each(function() {


            var self = this;
            var value = $(this).val();
            var url = $(this).data('url');
            var id = $(this).attr('id');
            var target = $(this).data('target');

            console.log("[mediapicker] init from button with id " + id + " with target " + target + " | modal: " + settings.modal);

            var selected = {};

            // Wrapper
            var $wrapper = $("<div class='_mediapicker-wrapper'></div>");

            // Preview Container
            /*
            var $previewContainer = $('<div>', {class: 'mediapicker-preview'});
            var $previewPlaceholder = $('<div>', { class: 'mediapicker-preview-placeholder'})
                .html($('<i>', {class: 'fa fa-4x fa-plus'}))
                .appendTo($previewContainer)
                .hide()
                .on('click', function(ev) {
                    $(self).trigger('mediapicker.open', ev);
                });
            var $previewImg = $('<img>', { class: 'mediapicker-preview-img', 'data-fileid': null, 'data-filename': null, 'data-url': null})
                .appendTo($previewContainer)
                .hide();
            var $previewLabel = $('<div>', { class: 'mediapicker-preview-label'})
                .appendTo($previewContainer)
                .hide();
            */

            // MediaPicker container
            var $container = $('<div>', { class: 'mediapicker-container'});

            var template = '<div class="row"> \
                <div class="col-sm-4 mediapicker-tree-container"></div> \
                <div class="col-sm-8 mediapicker-files-container"></div> \
                <!-- <div class="col-sm-3 mediapicker-selected-container"></div> --> \
                </div>';
            var containerHtml = _.template(template)();

            // Modal
            //if (settings.modal) {
                var modalId = 'media-picker-modal-' + id;

                var modalTemplate = '<div class="modal show fade" id="<%= modalId %>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"> \
                    <div class="modal-dialog modal-lg" role="document" style="width: 80%;"> \
                    <div class="modal-content"> \
                    <div class="modal-header"> \
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button> \
                <h4 class="modal-title" id="myModalLabel">Media Gallery</h4> \
                </div> \
                <div class="modal-body"><%= modalBody %></div>  \
                <div class="modal-footer"> \
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button> \
                    <button type="button" class="select btn btn-primary">Select</button> \
                </div> \
                </div> \
                </div> \
                </div>';

                containerHtml = _.template(modalTemplate)({
                    modalId: modalId,
                    modalBody: containerHtml
                });

            //}
            $container.html(containerHtml);

            // Sub Containers
            var $treeContainer = $container.find('.mediapicker-tree-container').first();
            var $filesContainer = $container.find('.mediapicker-files-container').first();
            var $selectedContainer = $container.find('.mediapicker-selected-container').first();

            // Tree
            var $tree = $('<div>', { class: 'mediapicker-tree' })
                .on('changed.jstree', function (e, data) {
                    var i, j, r = [];
                    //console.log(data);
                    if (data.action === "select_node") {
                        for(i = 0, j = data.selected.length; i < j; i++) {
                            r.push(data.instance.get_node(data.selected[i]).id);
                        }

                        var path = r.join('/');
                        console.log('[mediapicker] Selected: ' + r.join(', '), 'Path: ' + path);

                        var config = 'images';
                        var url = settings.filesUrl + '?id='+path;

                        $.ajax({
                            method: 'GET',
                            url: url,
                            dataType: 'json',
                            data: {'selected': r },
                            beforeSend: function() {
                            },
                            success: function(data) {

                                // no files in folder
                                if (data.length === 0) {
                                    $filesContainer.html('<div class="mediapicker-message">No files in folder ' + path + '</div>');
                                    return;
                                }

                                // list files

                                var inputType = (settings.multiple === true) ? 'checkbox' : 'radio';

                                var $table = $('<table>', { 'class': 'table table-hover'});
                                for(var i in data) {
                                    var file = data[i];
                                    var $row = $('<tr>', {
                                        //'data-input': $(self).attr('id'),
                                        'data-img-src': file.icon,
                                        'data-id': file.id,
                                        'data-name': file.text,
                                        'data-src': file.thumbUrl, // file.icon,
                                        'class': 'mediapicker-item'

                                    });


                                    if (file.icon) {
                                        $('<td />', {'width': 60}).html($('<img>', {src: file.icon, class: 'mediapicker-item-preview'}))
                                            .appendTo($row);
                                    } else {
                                        $('<td />', {'width': 60}).html($('<i class="fa fa-file-o"></i>'))
                                            .appendTo($row);
                                    }
                                    $('<td />').html($('<span>', { class: 'mediapicker-item-name'}).html(file.text))
                                        .appendTo($row);

                                    $('<td />', {'width': 20}).html($('<input>', { class: 'mediapicker-item-input', name: 'select', type: inputType, value: 1 }))
                                        .appendTo($row);

                                    $row.appendTo($table);
                                }
                                $filesContainer.html($table);

                                $filesContainer.off('click', '.mediapicker-item');
                                $filesContainer.on('click', '.mediapicker-item', function(ev) {
                                    console.log("picker item clicked");
                                    $(this).find('.mediapicker-item-input')
                                        .prop('checked', 'checked')
                                        .trigger('change');
                                });

                                // Capture click events on media items
                                $filesContainer.off('change', '.mediapicker-item-input');
                                $filesContainer.on('change', '.mediapicker-item-input', function(ev) {
                                    ev.preventDefault();

                                    var data = $(ev.target).closest(".mediapicker-item").data();

                                    var id = data.id;
                                    var name = data.name;
                                    var src = data.imgSrc;
                                    var val = !!$(this).attr('checked');

                                    console.log("Media Item selected: " + path + " -> " + id);

                                    if (!settings.multiple) {
                                        selected = [];
                                    }
                                    selected.push(id);
                                    console.log("Selected", selected);

                                    /*
                                    // remove selected class from previously selected items
                                    $filesContainer.find('.mediapicker-item.selected').removeClass('selected');
                                    // mark current item as selected
                                    $(this).addClass('selected');
                                    // show preview of selected item in selectedContainer
                                    if (src) {
                                        $selectedContainer
                                            .html($('<img>', { class: 'selected', src: src, title: name, style: 'max-width: 100%;'}));
                                    } else {
                                        $selectedContainer.html(name);
                                    }
                                    */

                                    // update the original input field and trigger 'change'
                                    /*
                                    $(self)
                                        .val(id)
                                        //.attr('data-url', src)
                                        .data('fileid', id)
                                        .data('filename', name)
                                        .data('fileurl', src)
                                        .trigger('change')
                                    ;
                                    */

                                    //return false;
                                });

                            }
                        });

                    }

                })
                .appendTo($treeContainer);

            // Actions
            /*
            var $actionsContainer = $('<div>', {class: 'mediapicker-actions'})
                // Add select action with event handler
                .append($('<a>', { href: '#', class: 'btn btn-sm btn-default mediapicker-action-select'}).html(settings.textSelect))
                .on('click', '.mediapicker-action-select', function(ev) {
                    ev.preventDefault();
                    $(self).trigger('mediapicker.open', ev);
                    return false;
                })
                // Add remove action with event handler
                .append($('<a>', { href: '#', class: 'btn btn-sm btn-default mediapicker-action-remove'}).html(settings.textRemove))
                .on('click', '.mediapicker-action-remove', function(ev) {
                    ev.preventDefault();
                    $(self)
                        .val('')
                        .data({
                            fileid: null,
                            filename: null,
                            fileurl: null
                        })
                        .trigger('change');
                    return false;
                });
            */

            // modify DOM
            $(this)
                //.before($previewContainer)
                .wrap( $wrapper )
                .after($container)
                //.after($actionsContainer)
                .on('click', function(ev) {


                    console.log("select media button for " + target + " clicked");

                    console.log("[mediapicker] init modal " + modalId);
                    $('#' + modalId).modal({
                        keyboard: true,
                        show: true,
                        backdrop: true,
                    }).on('shown.bs.modal', function() {

                        $('#' + modalId + ' .mediapicker-tree').jstree({
                            "core" : {
                                "themes" : {
                                    //"variant" : "large"
                                },
                                'data' : {
                                    'url': function (node) {
                                        return settings.treeUrl;
                                    },
                                    'data': function (node) {
                                        return {'id': node.id};
                                    }
                                }
                            },
                            "checkbox" : {
                                "keep_selected_style" : false
                            },
                            "plugins" : [ "wholerow", "changed" ] // , "checkbox"
                        });
                    });

                    $('#' + modalId + " button.select").on('click', function() {
                        console.log("Update target " + target + " with value " + selected.join(','));
                        $(target).val(selected.join(','));

                        $(this).closest('.modal').modal('hide');
                    });

                    ev.preventDefault();
                    return false;
                })
                //.attr('type', 'hidden')
            ;

            /*
            $(this).on('change', function(ev) {
                console.log("[mediapicker] Input has been updated", $(this).data());

                var val = $(this).val();
                var data = $(this).data();
                if (val && data && data.fileurl && data.fileid && data.filename) {
                    $previewContainer
                        .find('.mediapicker-preview-img')
                        .data(data)
                        .attr('src', data.fileurl)
                        .show();
                    $previewContainer
                        .find('.mediapicker-preview-label')
                        .html(data.filename)
                        .show();
                    $previewContainer.find('.mediapicker-preview-placeholder').hide();
                    $actionsContainer.find('.mediapicker-action-remove').show();
                } else {
                    $previewContainer.find('.mediapicker-preview-placeholder').show();
                    $previewContainer.find('.mediapicker-preview-img').hide();
                    $previewContainer.find('.mediapicker-preview-label').hide();
                    $actionsContainer.find('.mediapicker-action-remove').hide();
                }
            });
            $(this).trigger('change');
            */


        });

    };

}( jQuery ));
 