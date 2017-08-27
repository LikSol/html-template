'use strict';

// From: https://github.com/bezoerb/postcss-image-inliner/blob/8b825acebace2f1567195b49e47c0d454de4a3ae/index.js#L69
var URL_PROPERTY_PATTERN = /^(background(?:-image)?)|(content)|(cursor)/;
// From: https://github.com/postcss/postcss-url/blob/a9d1d4307b061210b1e051d1c2e9c481ca6afbf5/index.js#L26-L29
var UrlsPatterns = [
    /(url\(\s*['"]?)([^"')]+)(["']?\s*\))/g,
    /(AlphaImageLoader\(\s*src=['"]?)([^"')]+)(["'])/g,
]

const stylelint = require("stylelint")
const report = require("stylelint/lib/utils/report");

const ruleName = "plugin/check-url"
const messages = stylelint.utils.ruleMessages(ruleName, {
    expected: selectorValue =>
        `Expected img url "${selectorValue}" to be local`,
});

module.exports = stylelint.createPlugin(ruleName, function(config, options) {
    return function(root, result) {

        const allowedChars = "a-z0-9A-Z-"
        const regex = new RegExp(`^[${allowedChars}]+[./${allowedChars}]+\\.(png|jpg|svg)$`)

        root.walkDecls(function (decl) {
            if (!decl.prop.match(URL_PROPERTY_PATTERN)) return

            UrlsPatterns.some(function (pattern) {
                let parsed
                if (!(parsed = pattern.exec(decl.value))) return

                const url = parsed[2]

                if (!url.match(regex) || url.match(/\.\./)) {
                    report({
                        result,
                        ruleName,
                        message: messages.expected(url),
                        node: decl,
                        index: decl.sourceIndex
                    });
                }
            })
        })
    }
})

module.exports.ruleName = ruleName
module.exports.messages = messages
