/*
gulp lint-css
grunt htmlhintplus
gulp lint-html
 */

const gulp = require('gulp');

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

