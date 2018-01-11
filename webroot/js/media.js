// https://github.com/jquery-boilerplate/jquery-boilerplate/blob/master/src/jquery.boilerplate.js
(function(window, document, $, _, Backbone){

    //var CakeMedia = window.CakeMedia || {};
    var _name_ = "CakeMedia";
    var defaults = {
        files: [],
        dirs: [],
        mediaUrl: null
    };

    // The actual plugin constructor
    function CakeMedia ( element, options ) {

        console.log("creating new CakeMedia instance", typeof element, element, options);

        this.element = element;
        this.files = new Backbone.Collection();
        this.dirs = new Backbone.Collection();
        this.components = ['ajax', 'paging'];
        this.view = null;

        // jQuery has an extend method which merges the contents of two or
        // more objects, storing the result in the first object. The first object
        // is generally empty as we don't want to alter the default options for
        // future instances of the plugin
        this.settings = $.extend( {}, defaults, options );
        this._defaults = defaults;
        this._name = _name_;
        this.init();
    }

    // Avoid Plugin.prototype conflicts
    _.extend( CakeMedia.prototype, {
        init: function() {
            console.log("[CakeMedia] init", this.settings);


            // jQuery element instance
            if (this.element instanceof jQuery) {
                console.log("[CakeMedia] element from jquery object");
                this.$element = this.element;
            }
            else if (typeof this.element === "object") {
                console.log("[CakeMedia] element from object");
                this.$element = $(this.element);
            }
            else if (typeof this.element === "string") {
                console.log("[CakeMedia] element from string");
                this.$element = $(this.element);
            }
            else {
                console.error("[CakeMedia] element is undefined")
                this.$element = undefined;
                return false;
            }

            // parse table
            //this._initTable();
            //this.table.setElement(this.$element);

            this.mediaUrl = this.mediaUrl || this.$element.data('mediaUrl');
            this.baseUrl = this.baseUrl || this.$element.data('mediaBaseurl');
            console.info('Media Data URL: ' + this.mediaUrl);
            console.info('Media Base URL: ' + this.baseUrl);
            console.log("[CakeMedia] init complete; settings:", this.settings);

            // init media view
            this.view = new CakeMedia.MediaView({
                media: this,
                el: this.$element,
                baseUrl: this.baseUrl
            });
            this.view.setElement(this.$element);
            this.view.render();


            this.setPath('/')
        },

        setPath: function(path) {
            console.log("[CakeMedia] Set path to", path);

            var self = this;
            this.getJSON(path)
                .then(function(data) {
                    console.log("AJAX SUCCESS", data);
                    self.view.onDirectoryOpen(data);
                })
                .fail(function(err) {
                    console.log("AJAX ERROR", err);
                })
        },

        buildApiUrl: function(path) {
            return this.mediaUrl + '?path=' + path;
        },

        getJSON: function(path, ajaxSettings) {

            var url = this.buildApiUrl(path);
            console.log("[app] Http.getJSON from " + url);
            var defaultSettings = {
                method: "GET",
                url: url,
                async: true,
                cache: false,
                global: true,
                crossDomain: true,
                dataType: "json",
                contentType: "application/json; charset=utf-8",
                headers: {
                },
                success: function(data, textStatus, jqXHR) {
                    //console.log("[app:http:getJSON] success", textStatus);
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    //console.log("[app:http:getJSON] error", textStatus);
                }
            };

            var settings =  _.extend(defaultSettings, ajaxSettings);
            return $.ajax(settings);
        },

        _initTable: function() {

            if (this.$element) {
                var tblData = this.$element.data();
                _.each(this.components, function(name) {
                    var data = {};
                    _.each(tblData, function(v, k) {
                        if (k !== name && k.indexOf(name) === 0) {
                            var key = k[name.length].toLowerCase() + k.substr(name.length+1); // strip prefix and lowercase first letter
                            data[key] = v;
                        }
                    });
                    this.settings[name] = data;
                }, this);
            } else {
                console.warn("[CakeMedia] No table element given. Creating table.");
                this.$element = $('<table>', {'class': 'table'});
            }

        },

        _initColumns: function(files) {

            console.log("[CakeMedia] init files");

            files = files || this.settings.files || {};
            if (files instanceof Backbone.Collection) {
                //this.files.set(files.toJSON());
                this.files = files;
            }
            else if (typeof files.done === "function") { // check if this is an ajax request object
                console.info("[CakeMedia] init files: detect ajax: defer row initalization");

                var xhr = files;
                //this.files = new Backbone.Collection(); // create new collection
                this._deferRows = true; // set the deferRows flag (skips dirs initalization, until triggered elsewhere)

                var self = this;
                xhr.done(function( data, textStatus, jqXHR ) { // wait for the ajax request to complete
                    console.log("[CakeMedia] converting files to Collection from ajax result", data);
                    self.files.set(data); // then populate collection with results
                    //self._initRows(); // and trigger row initialization
                });
            }
            else if (typeof files === "object" || typeof files === "array") { // we want a collection
                console.log("[CakeMedia] converting files to Collection", files);
                this.files.set(files);
            }
            else {
                console.warn("[CakeMedia] _initColumns: Unknown files type given (Skip)", files);
            }

            // attach update listener
            var self = this;
            this.files.on('update', function() {
                console.log("[CakeMedia] files updatedX");
                self._initRows();
            });

            if (this.files.url) {
                this._deferRows = true;
                this.files.fetch();
            }
        },

        _initRows: function(dirs) {

            console.log("[CakeMedia] init dirs");

            dirs = dirs || this.settings.dirs || {};
            if (dirs instanceof Backbone.Collection) {
                console.log("[CakeMedia] init dirs: from collection", dirs);
                //this.dirs.set(dirs.toJSON());
                this.dirs = dirs;
            }
            else if (typeof dirs.done === "function") { // check if this is an ajax request object
                var xhr = dirs;
                //this.dirs = new Backbone.Collection(); // create new collection

                var self = this;
                xhr.done(function( data, textStatus, jqXHR ) { // wait for the ajax request to complete
                    console.log("[CakeMedia] converting dirs to Collection from ajax result", data);
                    self.dirs.set(data); // then populate collection with results
                });
            }
            else if (typeof dirs === "object" || typeof dirs === "array") { // we want a collection
                this.dirs.set(dirs);
                console.log("[CakeMedia] converting dirs to Collection", dirs);
            }
            else {
                console.warn("[CakeMedia] _initRows: Unknown dirs type given (Skip)", dirs);
            }

            // attach update listener
            var self = this;
            this.dirs.on('update', function() {
                console.log("[CakeMedia] dirs updatedX");
                self.render();
            });

            if (this.dirs.url) {
                console.log("[CakeMedia] init dirs: fetch");
                this.dirs.fetch();
            }
        },

        render: function() {
            console.log("[CakeMedia] render");
            // TableView instance
            /*
             var table = CakeMedia.Table.extend( {
             el: this.$element
             } );
             this.table = new table(this);
             */
            // init table view
            //this.table.render();
        }

    } );

    /** VIEW **/
    CakeMedia.MediaView = Backbone.View.extend({

        template: _.template(
            '<div class="row">' +
                '<div class="col-md-8">' +
                    '<p>Path: <%= path %></p>' +
                    '<p><a href="#" class="parent">Parent dir</a></p>' +
                    '<div class="dirs"></div>' +
                    '<div class="files"></div>' +
                '</div>' +
                '<div class="col-md-4">' +
                '</div>' +
            '</div>'
        ),

        events: {
            'click a.parent': 'onClickParentDir'
        },

        initialize: function(settings) {
            console.log("[MediaView] INIT", settings);
            this.media = settings.media;
            this.data = settings.data || { path: null, files: [], dirs: [] }
            this.baseUrl = settings.baseUrl || '/';

            this.$preview = null;
        },

        render: function() {
            console.log("[MediaView] RENDER");
            this.reset();
            this.$el.html(this.template(this.data));

            //this.dirsView = new CakeMedia.FileListView({ type: 'dir', parent: this.data.path, files: this.data.dirs });
            this.dirsView = new CakeMedia.FileTableView({ type: 'dir', parent: this.data.path, files: this.data.dirs });
            this.$el.find('.dirs').html(this.dirsView.render().$el);

            //this.filesView = new CakeMedia.FileListView({ type: 'file', parent: this.data.path, files: this.data.files });
            this.filesView = new CakeMedia.FileTableView({ type: 'file', parent: this.data.path, files: this.data.files, baseUrl: this.baseUrl });
            this.$el.find('.files').html(this.filesView.render().$el);

            var self = this;
            this.listenTo(this.dirsView, 'dir.select', this.onDirectorySelect);
            this.listenTo(this.filesView, 'file.select', function(parent, file) {
                console.log("[media] file selected", parent, file)
            });

            // inject preview frame
            if (this.$preview === null) {
                this.$preview = $('<div>', { id: 'preview-container', })
                    .css({position: "fixed", top: 100, right: 10, width: "200px", height: "150px", "background-color": "transparent"})
                    .html("");
                this.$el.after(this.$preview);
            }

            return this;
        },

        reset: function() {
            if (this.dirsView) {
                this.stopListening(this.dirsView);
                //this.dirsView.close();
            }
            if (this.filesView) {
                this.stopListening(this.filesView);
                //this.filesView.close();
            }
        },

        _buildParentDir: function(path) {
            var tmp = path;

            if (tmp.lastIndexOf("/") != tmp.indexOf("/")) {
                var parts = tmp.split("/");
                parts.pop();
                parts.pop();
                tmp = parts.join("/") + "/";
            }
            return tmp;
        },

        onClickParentDir: function(ev) {
            var $target = $(ev.target);
            var dir = this.data.path;
            if (dir && dir != "/") {
                console.log("[media] parent dir CLICKED", 'dir', dir, this._buildParentDir(dir));
                this.dirsView.trigger('dir.select', this._buildParentDir(dir));
            }

            ev.preventDefault();
            ev.stopPropagation();
            return false;
        },

        onDirectorySelect: function(parent, dir) {
            console.log("[media] dir selected", parent, dir);
            if (parent && dir) {
                this.media.setPath(parent + dir);
            }
            else if (parent) {
                this.media.setPath(parent);
            }
            else {
                this.media.setPath('/');
            }
        },

        onDirectoryOpen: function(data) {
            this.data = data;
            this.render();
        }
    });

    CakeMedia.FileListView = Backbone.View.extend({

        tagName: 'ul',

        template: _.template('<li><a href="#" title="<%= parent %> / <%= name %>" data-parent="<%= parent %>" data-<%= type %>="<%= name %>"><%= name %></a></li>'),

        events: {
            'click li a': 'onClick'
        },

        initialize: function(settings) {
            this.parent = settings.parent || undefined;
            this.type = settings.type || undefined;
            this.files = settings.files || [];
        },

        render: function() {
            this.$el.html("");

            var self = this;
            console.log("[file-list] RENDER", this.files);
            _.each(this.files, function(file) {
                console.log("append file", file);
                self.$el.append(self.template({ type: self.type, parent: self.parent, name: file }));
            });

            return this;
        },

        onClick: function(ev) {
            $target = $(ev.target);
            var item = $target.data(this.type);

            console.log("[file-list] CLICKED", this.type, item);
            this.trigger(this.type + '.select', this.parent, item);

            ev.preventDefault();
            ev.stopPropagation();
            return false;
        },

        setFiles: function(files) {
            this.files = files;
            return this;
        }
    });

    CakeMedia.FileTableView = Backbone.View.extend({

        tagName: 'table',

        template: _.template('<thead></thead><tbody></tbody>'),

        events: {
            'click td a': 'onItemClick',
            'mouseover td a': 'onItemHover'
        },

        initialize: function(settings) {
            this.parent = settings.parent || undefined;
            this.type = settings.type || undefined;
            this.files = settings.files || [];
            this.baseUrl = settings.baseUrl || undefined;
        },

        render: function() {
            this.$el.html("");
            this.$el.addClass('table table-condensed');

            var self = this;
            console.log("[file-table] RENDER", this.files);
            _.each(this.files, function(file) {
                console.log("append file", file);
                self.$el.append((new CakeMedia.FileTableItemView({
                    parent: self.parent,
                    type: self.type,
                    baseUrl: self.baseUrl,
                    item: file
                })).render().$el);
            });

            this.$el.find('tr td img[data-url]').each(function() {
                var url = $(this).data('url');
                if (url && url !== "#") {
                    $(this).attr('src', url);
                }
            });

            return this;
        },

        setFiles: function(files) {
            this.files = files;
            return this;
        },

        onItemClick: function(ev) {
            $target = $(ev.target);
            var item = $target.data(this.type);
            var url = $target.data('url');

            console.log("[file-table-item] CLICKED", this.type, item, url);
            this.trigger(this.type + '.select', this.parent, item, url);

            ev.preventDefault();
            ev.stopPropagation();
            return false;
        },

        onItemHover: function(ev) {
            $target = $(ev.target);
            var item = $target.data(this.type);
            var url = $target.data('url');

            if (this.type == "file" && url && url !== '#'
                && (item.indexOf('.jpeg') > 0 || item.indexOf('.jpg') > 0 || item.indexOf('.png') > 0 || item.indexOf('.gif') > 0)) {
                //this.trigger(this.type + '.select', this.parent, item, url);
                console.log("[file-table-item] HOVER", this.type, item, url);
                $('#preview-container')
                    .html($('<img>', { src: url, alt: item, title: item, width: 100, height: 100}))
                    .append($('<p>'). html(item));

                $target.closest('tr').find('td.icon img').attr('src', url);
            } else if (this.type == "file") {
                $('#preview-container').html('No preview');
            } else {
                $('#preview-container').html('');
            }

            ev.preventDefault();
            ev.stopPropagation();
            return false;
        },
    });

    CakeMedia.FileTableItemView = CakeMedia.FileListView.extend({

        tagName: 'tr',

        template: _.template(
            '<td width="20px" class="icon">&nbsp;</td>' +
            '<td>' +
                //'<img src="" data-url="<%= url %>" title="<%= title %>" data-parent="<%= parent %>" data-name="<%= name %>" width="42" height="42" />' +
                '<a href="#" data-url="<%= url %>" title="<%= title %>" data-parent="<%= parent %>" data-<%= type %>="<%= name %>"><%= name %></a>' +
            '</td>'
        ),

        initialize: function(settings) {
            this.parent = settings.parent || undefined;
            this.type = settings.type || undefined;
            this.item = settings.item || undefined;
            this.baseUrl = settings.baseUrl || undefined;
            this.itemUrl = (this.baseUrl) ? this.baseUrl + this.parent + this.item : '#';
        },

        render: function() {
            this.$el.html(this.template({
                type: this.type,
                parent: this.parent,
                name: this.item,
                title: this.parent + ' / ' + this.item,
                url: this.itemUrl
            }));

            if (this.type == "file"
                && (this.item.indexOf('.jpeg') > 0 || this.item.indexOf('.jpg') > 0 || this.item.indexOf('.png') > 0 || this.item.indexOf('.gif') > 0)) {

                //this.$el.find('td.icon').html($('<img>', {
                //    src: '',
                //    'data-src': this.itemUrl,
                //    alt: '', //this.item,
                //    title: this.item,
                //    width: 30,
                //    height: 30
                //}))
                this.$el.find('td.icon').html($('<i>', { 'class': 'fa fa-file-image-o'}));
            } else if (this.type == "file" && this.item.indexOf('.pdf') > 0) {
                this.$el.find('td.icon').html($('<i>', { 'class': 'fa fa-file-pdf-o'}));
            } else if (this.type == "file" && this.item.indexOf('.txt') > 0) {
                this.$el.find('td.icon').html($('<i>', { 'class': 'fa fa-file-text-o'}));
            } else if (this.type == "file" && this.item.indexOf('.zip') > 0) {
                this.$el.find('td.icon').html($('<i>', { 'class': 'fa fa-file-archive-o'}));
            } else if (this.type == "file" && this.item.indexOf('.tar.gz') > 0) {
                this.$el.find('td.icon').html($('<i>', { 'class': 'fa fa-file-archive-o'}));
            } else if (this.type == "file" && this.item.indexOf('.php') > 0) {
                this.$el.find('td.icon').html($('<i>', { 'class': 'fa fa-file-code-o'}));
            } else if (this.type == "file" && this.item.indexOf('.js') > 0) {
                this.$el.find('td.icon').html($('<i>', { 'class': 'fa fa-file-code-o'}));
            } else if (this.type == "file" && this.item.indexOf('.css') > 0) {
                this.$el.find('td.icon').html($('<i>', { 'class': 'fa fa-file-code-o'}));
            } else if (this.type == "file" && this.item.indexOf('.less') > 0) {
                this.$el.find('td.icon').html($('<i>', { 'class': 'fa fa-file-code-o'}));
            } else if (this.type == "file") {
                this.$el.find('td.icon').html($('<i>', { 'class': 'fa fa-file-o'}));
            } else {
                this.$el.find('td.icon').html($('<i>', { 'class': 'fa fa-folder-o'}));
            }

            return this;
        },


    });

    /** FILE SYSTEM **/

    CakeMedia.File = Backbone.Model.extend({
       defaults: {
           filename: null
       }
    });
    CakeMedia.FileCollection = Backbone.Collection.extend({
        model: CakeMedia.CakeMediaFileModel
    });


    /** COMPONENT **/

    CakeMedia.Component = (function() {
        var Component = {
            Component: function() {
                console.log("construct component")
            },

            extend: function(obj) {
                _.extend(this, obj);
            }
        };
        return Component;
    })();

    _.extend(CakeMedia.Component.prototype, {

    });

    $.fn[_name_] = function( options ) {
        return this.each( function() {

            // @TODO: check if selector is a TABLE tag
            if (this.tagName !== "TABLE") {
                console.warn("CakeMedia jquery selector is not a TABLE: " + this.tagName);
            }

            if ( !$.data( this, _name_ ) ) {
                //$.data( this, _name_, new CakeMedia( $(this), options ) );
                $.data( this, _name_, new CakeMedia( this, options ) );
            }
        } );
    };

    window.CakeMedia = CakeMedia;

})(window, document, jQuery, _, Backbone);