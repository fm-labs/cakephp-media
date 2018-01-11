// https://github.com/jquery-boilerplate/jquery-boilerplate/blob/master/src/jquery.boilerplate.js
(function(window, document, $, _, Backbone){

    //var CakeMedia = window.CakeMedia || {};
    var _name_ = "CakeMedia";
    var defaults = {
        files: [],
        dirs: []
    };

    // The actual plugin constructor
    function CakeMedia ( element, options ) {

        console.log("creating new CakeMedia instance", element, options);

        this.element = element;
        this.files = new Backbone.Collection();
        this.dirs = new Backbone.Collection();
        this.components = ['ajax', 'paging'];

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
                this.$element = this.element;
            }
            else if (this.element === "string") {
                this.$element = $(this.element);
            } else {
                this.$element = undefined;
            }

            // parse table
            //this._initTable();

            this.table.setElement(this.$element);

            console.log("[CakeMedia] init complete; settings:", this.settings);
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

    CakeMedia.File = Backbone.Model.extend({
       defaults: {
           filename: null
       }
    });
    CakeMedia.FileCollection = Backbone.Collection.extend({
        model: CakeMedia.CakeMediaFileModel
    });

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
                $.data( this, _name_, new CakeMedia( this, options ) );
            }
        } );
    };

    window.CakeMedia = CakeMedia;

})(window, document, jQuery, _, Backbone);