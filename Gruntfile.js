/*
gulp lint-css
grunt htmlhintplus
gulp lint-html
 */

module.exports = function(grunt) {

  grunt.loadNpmTasks('grunt-htmlhint-plus');

    grunt.initConfig({
        htmlhintplus: {
            build: {
                options: {
                    force: true,
                    extendRules: true,
                    rules: {
                        // builtin
                        'id-class-value': false,
                        'href-abs-or-rel': false,
                    },
                    customRules: [
                    ],
                },
                src: 'html/index.html'
            }
        }
    });
}