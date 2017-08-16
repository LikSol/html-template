'use strict';

// Abbreviated example
var stylelint = require("stylelint")

const _ = require("lodash");
const mediaParser = require("postcss-media-query-parser");
const report = require("stylelint/lib/utils/report");

var ruleName = "plugin/media-query-order"
const messages = stylelint.utils.ruleMessages(ruleName, {
    expectedAfter: selectorValue =>
        `Expected atrule "${selectorValue}" to be positioned after regular rules`,
    expectedPattern: (selectorValue, pattern) =>
        `Expected media rule "${selectorValue}" to match pattern "${pattern}"`,
    expectedOrder: (current, previous) =>
        `Expected media rule "${current}" to be positioned before "${previous}" (in ascending order)`
});

module.exports = stylelint.createPlugin(ruleName, function(pattern, options) {
    return function(root, result) {

        let atrule
        root.some(function (node) {
            if (atrule) {
                if (node.type !== 'comment' && node.type !== 'atrule') {
                    report({
                        result,
                        ruleName,
                        message: messages.expectedAfter('@' + atrule.name + ' ' + atrule.params),
                        node: atrule,
                        index: atrule.sourceIndex
                    });
                    return true
                }
            }

            if (!atrule && node.type === 'atrule' && node.name !== 'import') {
                atrule = node
            }
        })

        let lastPixels = 0
        let previous

        root.walkAtRules(rule => {
            const current_pattern = mediaToString(mediaParser.default(rule.params));
            const allowed_pattern = mediaToString(mediaParser.default('screen and (min-width: 100px)'));

            if (current_pattern !== allowed_pattern) {
                // пока поддерживаем только простые варианты:
                // screen and (min-width: ...)
                return
            }
            var pixels = check(rule);
            if (pixels <= lastPixels) {
                report({
                    result,
                    ruleName,
                    message: messages.expectedOrder(rule.params.toString(), previous.params.toString()),
                    node: rule,
                    index: rule.sourceIndex
                });
            }

            previous = rule
            lastPixels = pixels
        });

        function mediaToString(result) {
            var resultArray = []

            result.walk(function (nodeValue) {
                if (!nodeValue.nodes) {
                    if (nodeValue.type === 'value') {
                        if (!/^[0-9]+px/.test(nodeValue.value)) {
                            throw "Unknown value " + nodeValue.value;
                        }
                        resultArray.push('...');
                    } else {
                        resultArray.push(nodeValue.value)
                    }
                }
            })

            return resultArray.join(" ")
        }

        function getMediaPixels(result) {
            var pixels = null;
            result.walk(function (nodeValue) {
                if (!nodeValue.nodes) {
                    if (nodeValue.type === 'value') {
                        if (!/^[0-9]+px/.test(nodeValue.value)) {
                            throw "Unknown value " + nodeValue.value;
                        }
                        pixels = parseInt(nodeValue.value);
                    }
                }
            })

            return pixels
        }

        function check(rule) {
            const current = mediaParser.default(rule.params);
            const current_string = mediaToString(current)
            const pattern = mediaParser.default('screen and (min-width: 100px)');
            const pattern_string = mediaToString(pattern)

            if (current_string !== pattern_string) {
                report({
                    result,
                    ruleName,
                    message: messages.expectedPattern(current_string, pattern_string),
                    node: rule,
                    index: rule.sourceIndex
                });
            }

            return getMediaPixels(current);
        }

    }
})

module.exports.ruleName = ruleName
module.exports.messages = messages
