/*
gulp test
grunt htmlhintplus
gulp lint-html
 */

const gulp = require('gulp');
// https://stackoverflow.com/a/27535245/1775065
gulp.Gulp.prototype.__runTask = gulp.Gulp.prototype._runTask;
gulp.Gulp.prototype._runTask = function(task) {
    this.currentTask = task;
    this.__runTask(task);
}

const fs = require('fs');
const merge = require('merge-deep');
const shell = require('shelljs');
const expect = require('gulp-expect-file')

let local_config = {};

if (fs.existsSync('./lint-config-local.js')) {
    local_config = require('./lint-config-local.js')
}

gulp.task('lint-css', function lintCssTask() {
    const config = merge({
        pages: local_config.global.pages.concat(['layout'])
    }, local_config['lint-css'])

    let entries = {}
    config.pages.forEach(function (page) {
        entries[page] = 'web/frontend/' + page + '/' + page + '.css'
    })
    entries.components = 'web/frontend/component/components.css'

    let expected_files = []

    Object.keys(entries).forEach(function (key) {
        expected_files.push(entries[key])
    })

    let existing_files = require('glob').sync('web/frontend/**/*.css')

    const gulpStylelint = require('gulp-stylelint')
    const stylelintConfigBase = require('./stylelint.config.base')

    const streams = require('merge2')()

    const path = require('path')

    const postcss = require('gulp-postcss')
    const doiuseDisable = function(prop, value) {
        return {
            prop: prop, value: value,
            method: 'wrap', comment: {before: 'doiuse-disable', after: 'doiuse-enable'}
        }
    }
    const postcss_add_comments = require(__dirname + '/postcss/postcss-add-comments/index.js')([
        doiuseDisable('outline', 'none'),
        doiuseDisable('appearance', 'none'),
        doiuseDisable('-webkit-appearance', 'none'),
        doiuseDisable('-moz-appearance', 'none'),
        doiuseDisable('column-count', /[0-9]+/),
        doiuseDisable('column-gap', /[0-9]+px/),
        doiuseDisable('display', 'flex'),
    ])

    existing_files.forEach(function (file) {
        const name = path.basename(path.dirname(file))
        let stylelintConfig;
        if (config.parts[name]) {
            stylelintConfig = merge(stylelintConfigBase, config.parts[name].stylelint)
        } else {
            stylelintConfig = stylelintConfigBase
        }

        stream = gulp.src(file)
            .pipe(postcss([
                postcss_add_comments
            ]))
            .pipe(gulpStylelint({
                config: stylelintConfig,
                failAfterError: false,
                reporters: [
                    {formatter: 'string', console: true}
                ]
            }))

        streams.add(stream)
    })

    return streams
        .pipe(expect({errorOnFailure: true}, expected_files))


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
        pages: local_config.global.pages
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

gulp.task('test', function (cb) {
    const runSequence = require('run-sequence');

    runSequence(
        'lint-git',
        'build-pages',
        'lint-css',
        cb
    )
});
