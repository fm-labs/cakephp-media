/*global module:false*/
module.exports = function(grunt) {

  // Project configuration.
  grunt.initConfig({
    // Metadata.
    pkg: grunt.file.readJSON('package.json'),
    banner: '/*! <%= pkg.title || pkg.name %> - v<%= pkg.version %> - ' +
      '<%= grunt.template.today("yyyy-mm-dd") %>\n' +
      '<%= pkg.homepage ? "* " + pkg.homepage + "\\n" : "" %>' +
      '* Copyright (c) <%= grunt.template.today("yyyy") %> <%= pkg.author.name %>;' +
      ' Licensed <%= _.pluck(pkg.licenses, "type").join(", ") %> */\n',
    // Task configuration.
    less: {
      development: {
        options: {
          paths: ['webroot/less'],
          banner: '/** <%= pkg.title || pkg.name %> - v<%= pkg.version %> **/\n'
        },
        files: {
          'webroot/css/filebrowser.css': 'webroot/less/filebrowser.less',
          'webroot/css/fileman.css': 'webroot/less/fileman.less'
        }
      },
      production: {
        options: {
          paths: ['webroot/less'],
          compress: true,
          plugins: [
            new (require('less-plugin-autoprefix'))({browsers: ["last 2 versions"]}),
            new (require('less-plugin-clean-css'))({ advanced: true })
          ]
        },
        files: {
          'webroot/css/filebrowser.min.css': 'webroot/less/filebrowser.less',
          'webroot/css/fileman.min.css': 'webroot/less/fileman.less'
        }
      }
    },
    watch: {
      assets: {
        files: [
          'webroot/less/*.less',
        ],
        tasks: ['less:development'],
        options: {
          spawn: false
        }
      }
    }
  });

  // These plugins provide necessary tasks.
  grunt.loadNpmTasks('grunt-contrib-watch');
  grunt.loadNpmTasks('grunt-contrib-less');

  // Default task.
  grunt.registerTask('default', ['less']);

};
