/*
gulp lint-css
grunt htmlhintplus
gulp lint-html
 */

const gulp = require('gulp');
gulp.Gulp.prototype.__runTask = gulp.Gulp.prototype._runTask;
gulp.Gulp.prototype._runTask = function(task) {
    this.currentTask = task;
    this.__runTask(task);
}

const fs = require('fs');
const merge = require('merge-deep');
const shell = require('shelljs');

let local_config = {};

if (fs.existsSync('./lint-config-local.js')) {
    local_config = require('./lint-config-local.js')
}

gulp.task('lint-css', function lintCssTask() {
    const gulpStylelint = require('gulp-stylelint');

    return gulp
        .src('html/css/layout.css')
        .pipe(gulpStylelint({
            reporters: [
                {formatter: 'string', console: true}
            ]
        }));
});

gulp.task('lint-html', function() {
    const posthtml = require('gulp-posthtml');

    var Lint = require('./posthtml/plugins/lint/index.js');
    var MarkParent = require('./posthtml/plugins/mark-parent/index.js');

    var plugins = [
        MarkParent(),
        Lint({
            rules: [
                __dirname + '/posthtml/plugins/lint/rules/top-level-tags-must-have-container.js',
                __dirname + '/posthtml/plugins/lint/rules/top-level-tags-must-have-meaningful-class.js',
            ]
        }),
    ];

    return gulp.src('html/index.html')
        .pipe(posthtml(plugins))
        ;

})

/**
 * Проверяет, что в проекте изменены только разрешенные к изменению файлы
 */
gulp.task('lint-git', function () {
    const config = merge({
        allow: [
            /^web\/frontend\//,
            /^apps\/main\/views\/layouts\/layout\.php$/,
            /^apps\/main\/views\/template\/[a-z]+\.php$/,
        ],
        ignore: [],
        sha: 'be3391e'
    }, local_config[this.currentTask.name])

    const result = shell.exec('git diff --name-only ' + config.sha + ' master@{0}', {silent:true});

    if (result.code !== 0) {
        throw new Error("Failed to run git: \n" + result.stderr);
    }

    const files = result.stdout;

    files.split("\n").forEach(function (line) {
        if (!line.trim()) return

        let matched = false

        config.allow.forEach(function (regex) { if (line.match(regex)) { matched = true }})
        config.ignore.forEach(function (str) { if (line === str) {matched = true }})

        if (!matched) {throw new Error("Line does not match: " + line)}
    })
})

gulp.task('build-pages', function () {
    const config = merge({
        domain: null,
        scheme: 'http',
        pages: []
    }, local_config[this.currentTask.name])

    const download = require("gulp-download-stream");

    config.pages.forEach(function (page) {
        let url = config.scheme + "://" + config.domain + '/template/' + page + '.html';
        download({
            url: url,
            file: page + '.html'
        })
            .pipe(gulp.dest("html/"));
    })
})

gulp.task('test', ['lint-git', 'lint-css', 'lint-html']);
