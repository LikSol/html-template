"use strict"

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

const chalk = require('chalk');
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
        if (config.only && config.only !== key) return

        expected_files.push(entries[key])
    })

    let existing_files = require('glob').sync('web/frontend/**/*.css')

    // https://stackoverflow.com/questions/45740437/gulp-expect-file-runs-check-before-stylelint-lints-files-missing-file-error
    let missing, unexpected
    expected_files.forEach(function (item) {
        if (existing_files.indexOf(item) === -1) {
            missing = true
            console.log("Missing file " + item)
        }
    })
    existing_files.forEach(function (item) {
        if (expected_files.indexOf(item) === -1) {
            unexpected = true
            console.log("Unexpected file " + item)
        }
    })
    if (missing || unexpected) {
        throw new Error("Failed css files expectations")
    }

    const gulpStylelint = require('gulp-stylelint')
    const stylelintConfigBase = require('./stylelint.config.base')


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

    let streams

    existing_files.forEach(function (file) {
        const name = path.basename(path.dirname(file))
        if (config.only && config.only !== name) return

        if (!streams) {
            streams = require('merge2')()
        }

        let stylelintConfig;
        if (config.parts[name]) {
            stylelintConfig = merge(stylelintConfigBase, config.parts[name].stylelint)
        } else {
            stylelintConfig = stylelintConfigBase
        }

        let stream = gulp.src(file)
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

    if (!streams) {
        // https://github.com/gulpjs/gulp/issues/2010
        throw new Error("No css files found")
    }

    return streams
        // .pipe(expect({errorOnFailure: true}, expected_files))
});

gulp.task('lint-html', ['build-pages'], function() {
    const config = merge({
        pages: local_config.global.pages
    }, local_config['lint-html'])

    let entries = {}
    config.pages.forEach(function (page) {
        entries[page] = 'html/' + page + '.html'
    })

    let expected_files = []

    Object.keys(entries).forEach(function (key) {
        if (config.only && config.only !== key) return

        expected_files.push(entries[key])
    })

    let existing_files = require('glob').sync('html/*.html')

    const path = require('path')
    const posthtml = require('gulp-posthtml');
    const Lint = require('./posthtml/plugins/lint/index.js');
    const MarkParent = require('./posthtml/plugins/mark-parent/index.js');
    const plugins = [
        MarkParent(),
        Lint({
            rules: [
                __dirname + '/posthtml/plugins/lint/rules/top-level-tags-must-have-container.js',
                __dirname + '/posthtml/plugins/lint/rules/top-level-tags-must-have-meaningful-class.js',
            ]
        }),
    ];

    let streams
    let html_is_valid = true

    existing_files.forEach(function (file) {
        const name = path.basename(file, '.html')

        if (config.only && config.only !== name) return

        const grunt = shell.exec('grunt lint-html:' + file, {silent:true});

        if (grunt.code !== 0) {
            html_is_valid = false
            // убираем текущий файл из expected files - мы не будем направлять
            // его в posthtml, так как он не валидный
            expected_files = expected_files.filter(function (item) {
                return item !== file
            })
            console.log(chalk.yellow("HTML is not valid in " + file))
            console.log()
            console.log(chalk.red(grunt.stdout))

            return
        }

        if (!streams) {
            streams = require('merge2')()
        }

        let stream = gulp.src(file)
            .pipe(posthtml(plugins))
        ;

        streams.add(stream)
    })

    if (!streams) {
        // https://github.com/gulpjs/gulp/issues/2010
        throw new Error("No valid files found")
    }

    if (!html_is_valid) {
        console.log("Invalid html files found")
    }

    return streams
        .pipe(expect({errorOnFailure: true}, expected_files))
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
        domain: local_config.global.domain,
        scheme: 'http',
        pages: local_config.global.pages
    }, local_config[this.currentTask.name])

    const download = require("gulp-download-stream");

    let files = []
    config.pages.forEach(function (page) {
        files.push({
            file: page + '.html',
            url: config.scheme + "://" + config.domain + '/template/' + page + '.html'
        })
    })

    return download(files)
        .pipe(gulp.dest("html/"));

})

gulp.task('test', ['lint-css', 'lint-html', 'lint-git'])
