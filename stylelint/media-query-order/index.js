'use strict';

// Abbreviated example
const stylelint = require("stylelint")

const _ = require("lodash");
const mediaParser = require("postcss-media-query-parser");
const report = require("stylelint/lib/utils/report");

const ruleName = "plugin/media-query-order"
const messages = stylelint.utils.ruleMessages(ruleName, {
    expectedAfter: selectorValue =>
        `Expected atrule "${selectorValue}" to be positioned after regular rules`,
    expectedPattern: (selectorValue, pattern) =>
        `Expected media rule "${selectorValue}" to match pattern "${pattern}"`,
    expectedOrder: (current, previous) =>
        `Expected media rule "${previous}" to be positioned after "${current}" (in ascending order)`
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

            if (!atrule && node.type === 'atrule' && node.name === 'media') {
                atrule = node
            }
        })

        let lastPixels = 0
        let previous

        root.walkAtRules(rule => {
            if (rule.name !== 'media') return

            const current_pattern = mediaToString(mediaParser.default(rule.params));
            const allowed_pattern = mediaToString(mediaParser.default('screen and (min-width: 100px)'));

            if (current_pattern !== allowed_pattern) {
                report({
                    result,
                    ruleName,
                    message: messages.expectedPattern(rule.params.toString(), allowed_pattern),
                    node: rule,
                    index: rule.sourceIndex
                });
                return
            }

            let pixels = getMediaPixels(rule);
            if (pixels <= lastPixels) {
                report({
                    result,
                    ruleName,
                    message: messages.expectedOrder(rule.params.toString(), previous.params.toString()),
                    node: previous,
                    index: previous.sourceIndex
                });
            }

            previous = rule
            lastPixels = pixels
        });

        function mediaToString(result) {
            let resultArray = []

            result.walk(function (nodeValue) {
                if (!nodeValue.nodes) {
                    let type = nodeValue.type
                    let value = nodeValue.value
                    switch (type) {
                        case 'value':
                            if (!/^[0-9]+px/.test(value)) {
                                throw "Unknown value " + value;
                            }
                            resultArray.push('...');
                            break;
                        case 'media-feature':
                            if (value !== 'max-width' && value !== 'min-width') {
                                throw "Unknown media-feature " + value;
                            }
                            resultArray.push('min/max-width');
                            break;
                        default:
                            resultArray.push(value)
                            break;
                    }
                }
            })

            return resultArray.join(" ")
        }

        function getMediaPixels(rule) {
            let parsed = mediaParser.default(rule.params)
            let pixels = null;
            parsed.walk(function (nodeValue) {
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

    }
})

module.exports.ruleName = ruleName
module.exports.messages = messages
