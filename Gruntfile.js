"use strict"

/*
gulp lint-css
grunt htmlhintplus
gulp lint-html
 */


module.exports = function(grunt) {

    grunt.loadNpmTasks('grunt-htmlhint-plus');

    grunt.task.registerTask('lint-html', 'description', function (file) {
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
                    src: file
                }
            }
        })

        grunt.task.run('htmlhintplus')
    })
}

