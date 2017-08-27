"use strict"

process.on('unhandledRejection', (reason) => {
    console.log(reason);
});

const semver = require('semver');

if (!semver.gte(process.version, '7.8.0')) {
    console.log("Node version v7.8.0 or greater required")
    process.exit()
}

const gulp = require('gulp');
// https://stackoverflow.com/a/27535245/1775065
gulp.Gulp.prototype.__runTask = gulp.Gulp.prototype._runTask;
gulp.Gulp.prototype._runTask = function(task) {
    this.currentTask = task;
    this.__runTask(task);
}

const _ = require('lodash')
const path = require('path')
const merge = require('merge-deep');
const shell = require('shelljs');
const gutil = require("gulp-util");
const log = gutil.log;
const col = gutil.colors;
const mkdirp = require('mkdirp')
const projectConfig = require('./config')

// https://stackoverflow.com/questions/45740437/gulp-expect-file-runs-check-before-stylelint-lints-files-missing-file-error
function checkFileExpectations(expected_files, existing_files) {
    let missing, unexpected

    expected_files.forEach(function (item) {
        if (existing_files.indexOf(item) === -1) {
            missing = true
            log(col.red("Missing file " + item))
        }
    })
    existing_files.forEach(function (item) {
        if (expected_files.indexOf(item) === -1) {
            unexpected = true
            log(col.red("Unexpected file " + item))
        }
    })

    return !(missing || unexpected)
}

gulp.task('lint-css', function lintCssTask() {
    const config = projectConfig.getForTask(this.currentTask.name, {
        pages: projectConfig.get().global.pages.concat(['layout'])
    })

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

    if (!checkFileExpectations(expected_files, existing_files)) {
        throw new Error("Failed css files expectations")
    }

    const gulpStylelint = require('gulp-stylelint')
    const stylelintConfigBase = require('./stylelint.config.base')

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
            if (stylelintConfig.plugins) {
                for (let i in stylelintConfig.plugins) {
                    stylelintConfig.plugins[i] = stylelintConfig.plugins[i].replace('@root', __dirname)
                }
            }
        } else {
            if (name !== 'layout' && name !== 'component') {
                stylelintConfig = merge(stylelintConfigBase, {
                    plugins: [
                        __dirname + "/stylelint/top-selector-match-pattern",
                    ],
                    rules: {
                        "plugin/top-selector-match-pattern": {
                            patterns: [
                                {
                                    type: 'class',
                                    name: '.page__' + name,
                                    pattern: 'page__' + name
                                },
                            ]
                        },
                    }
                })
            } else {
                stylelintConfig = stylelintConfigBase
            }
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


gulp.task('lint-html', function (cb) {
    const runSequence = require('run-sequence')

    runSequence('build-pages', 'lint-html-real', cb)
})

gulp.task('lint-html-real', function() {
    const config = projectConfig.getForTask(this.currentTask.name, {
        pages: projectConfig.get().global.pages
    })

    let entries = {full: {}, nolayout: {}, layout: null}
    let expected_files = {full: [], nolayout: [], layout: null}

    config.pages.forEach(function (page) {
        let full = 'html/full/' + page + '.html'
        let nolayout = 'html/no-layout/' + page + '.html'

        entries.full[page] = full
        entries.nolayout[page] = nolayout

        if (config.only && config.only !== page) return

        expected_files.full.push(full)
        expected_files.nolayout.push(nolayout)
    })
    entries.layout = 'html/layout/layout.html'
    if (!config.only) {
        expected_files.layout = [entries.layout]
    }

    let existing_files = {full: [], nolayout: [], layout: null}
    existing_files.full = require('glob').sync('html/full/*.html')
    existing_files.layout = require('glob').sync(entries.layout)
    existing_files.nolayout = require('glob').sync('html/no-layout/*.html')

    for (const group of Object.keys(expected_files)) {
        if (!checkFileExpectations(expected_files[group], existing_files[group])) {
            throw new Error("Failed css files expectations")
        }
    }

    const posthtml = require('gulp-posthtml');
    const Lint = require('./posthtml/plugins/lint/index.js');
    const MarkParent = require('./posthtml/plugins/mark-parent/index.js');

    let streams
    let html_is_valid = true

    existing_files.full.forEach(function (file) {
        const name = path.basename(file, '.html')

        if (config.only && config.only !== name) return

        if (!config.gruntDisabled) {
            const grunt = shell.exec('grunt lint-html:' + file, {silent:true});

            if (grunt.code !== 0) {
                html_is_valid = false
                // убираем текущий файл из expected files - мы не будем направлять
                // его в posthtml, так как он не валидный
                expected_files.full = expected_files.full.filter(function (item) {
                    return item !== file
                })
                log(col.yellow("HTML is not valid in " + file))
                log(col.red(grunt.stdout))

                return
            }
        }

        if (!streams) {
            streams = require('merge2')()
        }

        const mandatory = config.lint.mandatory.concat(
            [
                {url: '/frontend/layout/layout.css', tag: 'link'},
                {url: '/frontend/layout/layout.js', tag: 'script'},
                {url: '/frontend/component/components.css', tag: 'link'},
                {url: '/frontend/' + name + '/' + name + '.css', tag: 'link'},
                {url: '/frontend/' + name + '/' + name + '.js', tag: 'script'},
            ]
        )

        const allowed = mandatory.concat(config.lint.allowed)
        let lintRules = {
            file: file,
            rules: [
                {
                    path: __dirname + '/posthtml/plugins/lint/rules/top-level-tags-must-have-container.js',
                },
                {
                    path: __dirname + '/posthtml/plugins/lint/rules/page-must-have-mandatory-libraries.js',
                    config: mandatory
                },
                {
                    path: __dirname + '/posthtml/plugins/lint/rules/html-fixed-input-structure.js',
                },
            ]
        }

        if (!config.lint.noAllowedCheckOn || config.lint.noAllowedCheckOn.indexOf(name) === -1) {
            lintRules.rules.push(
                {
                    path: __dirname + '/posthtml/plugins/lint/rules/page-allow-libraries.js',
                    config: allowed
                }
            )
        }

        const plugins = [
            MarkParent(),
            Lint(lintRules),
        ];

        log('Validating HTML in ' + col.magenta(file))

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
        log(col.red("Invalid html files found"))
    }

    let full_stream = streams

    let response = require('merge2')()

    response.add(full_stream)





    /*
     * LAYOUT
     */

    let file = entries.layout

    let plugins = [
        Lint({
            file: file,
            rules: [
                {
                    path: __dirname + '/posthtml/plugins/lint/rules/img-allow-src.js',
                    config: new RegExp('^/frontend/layout/[A-Za-z0-9-]+.(jpg|svg|png)$')
                },
                {
                    path: __dirname + '/posthtml/plugins/lint/rules/top-level-tags-must-have-meaningful-class.js',
                    config: {
                        patterns: [
                            {
                                name: '.l__*',
                                pattern: "l__[a-zA-Z0-9-]+",
                            }
                        ],
                        level: 'body'
                    },
                },
            ]
        }),
    ]

    let layout_stream = gulp.src(file)
        .pipe(posthtml(plugins))

    response.add(layout_stream)





    /*
    * NO LAYOUT
    */

    streams = null

    existing_files.nolayout.forEach(function (file) {
        const name = path.basename(file, '.html')

        if (config.only && config.only !== name) return

        if (!streams) {
            streams = require('merge2')()
        }

        const plugins = [
            Lint({
                file: file,
                rules: [
                    {
                        path: __dirname + '/posthtml/plugins/lint/rules/img-allow-src.js',
                        config: new RegExp('^/frontend/' + name + '/[A-Za-z0-9-]+.(jpg|svg|png)$')
                    },
                    {
                        path: __dirname + '/posthtml/plugins/lint/rules/top-level-tags-must-have-meaningful-class.js',
                        config: {
                            patterns: [
                                {
                                    name: '.page__*',
                                    pattern: "page__" + name,
                                }
                            ],
                            level: null
                        },
                    },
                ]
            }),
        ];

        let stream = gulp.src(file)
            .pipe(posthtml(plugins))
        ;

        streams.add(stream)
    })

    if (!streams) {
        // https://github.com/gulpjs/gulp/issues/2010
        throw new Error("No valid files found")
    }


    let nolayout_stream = streams

    response.add(nolayout_stream)


    return response
})

/**
 * Проверяет, что в проекте изменены только разрешенные к изменению файлы
 */
gulp.task('lint-git', function () {
    const config = projectConfig.getForTask(this.currentTask.name)

    const upstream_url_allowed = [
        'git@github.com:cronfy/html-template.git',
        'https://github.com/cronfy/html-template.git'
    ]
    const upstream_url_preferred = upstream_url_allowed[0]

    const upstream_url = shell.exec(`git remote get-url upstream`, {silent:true}).stdout.trim()
    if (upstream_url && upstream_url_allowed.indexOf(upstream_url) === -1) {
        console.log("Git remote 'upstream' must point to allowed url: ", upstream_url_allowed)
        throw "Incorrect remote 'upstream' configuration"
    }

    if (!upstream_url) {
        log("Adding remote upstream, url " + upstream_url_preferred)
        if (shell.exec(`git remote add upstream ${upstream_url_preferred}`, {silent:true}).code !== 0) {
            throw "Failed to add remote 'upstream'"
        }
    }

    if (shell.exec(`git remote update`, {silent:true}).code !== 0) {
        throw "Failed to update remotes"
    }

    const result = shell.exec(`git diff --name-only remotes/upstream/master master`, {silent:true});

    if (result.code !== 0) {
        throw new Error("Failed to run git: \n" + result.stderr);
    }

    const files = result.stdout;

    files.split("\n").forEach(function (line) {
        if (!line.trim()) return

        let matched = false

        config.allow.forEach(function (regex) { if (line.match(new RegExp(regex))) { matched = true }})

        if (config.ignore) {
            config.ignore.forEach(function (str) { if (line === str) {matched = true }})
        }

        if (!matched) {throw new Error("Line does not match: " + line)}
    })
})

gulp.task('build-pages', function (cb) {
    const config = projectConfig.getForTask(
        this.currentTask.name, {
            domain: projectConfig.get().global.domain,
            pages: projectConfig.get().global.pages,
            scheme: projectConfig.get().global.scheme,
        })

    const download = require("gulp-download-stream");

    let files = []
    config.pages.forEach(function (page) {
        files.push({
            file: 'full/' + page + '.html',
            url: config.scheme + "://" + config.domain + '/template/' + page + '.html'
        })
        files.push({
            file: 'no-layout/' + page + '.html',
            url: config.scheme + "://" + config.domain + '/template/' + page + '.html?layout=false'
        })
    })
    files.push({
        file: 'layout/layout.html',
        url: config.scheme + "://" + config.domain + '/template/layout/'
    })

    return download(files)
        .pipe(gulp.dest("html/"))
})

async function getScreenshotsOfWidth(width, pages, config, browser) {
    log('Starting taking screenshots at ' + width + 'px')
    const page = await browser.newPage()
    await page.setViewport({width: width, height: 0})
    for (const name of pages) {
        const dir = `preview/live/${name}`
        mkdirp.sync(dir, 0o755)
        await page.goto(
            config.global.scheme + '://' + config.global.domain + '/template/' + name + '.html',
            // чтобы виджеты facebook/vk прогрузились
            // https://github.com/GoogleChrome/puppeteer/issues/372
            // TODO: найти решение получше
            {waitUntil: 'networkidle', networkIdleTimeout: 3000 }
        )
        await page.screenshot({
            path: `${dir}/${width}.png`,
            fullPage: true
        });
        log('Captured ' + name + ' at ' + width + 'px')
    }
    await page.close()
    log('Finished taking screenshots at ' + width + 'px')
}

gulp.task('screenshot', function () {
    const config = projectConfig.getForTask('screenshot')

    const Throttle = require ('promise-parallel-throttle')

    const puppeteer = require('puppeteer');

    if (!config.global.resolutions.length) {
        log(col.red("No resolutions specified in config - no screenshots produced"))
        return
    }

    return puppeteer.launch().then(browser => {
        const tasks = []

        for (const width of config.global.resolutions) {
            const pages = []
            for (const page of config.global.pages) {
                try {
                    const resolutions = config.global.pageSettings[page].resolutions
                    if (resolutions.indexOf(width) === -1) {
                        continue
                    }
                } catch (E) {
                }
                if (config.global.excludePages.indexOf(page) !== -1) {
                    continue
                }
                pages.push(page)
            }

            if (!pages.length) continue

            tasks.push(function () {
                return getScreenshotsOfWidth(width, pages, config, browser)
            })
        }

        return Throttle
            .all(tasks, {maxInProgress: 2})
            .then(() => browser.close())
    })

})

gulp.task('test', ['lint-css', 'lint-html'])
