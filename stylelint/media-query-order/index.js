'use strict';

// Abbreviated example
var stylelint = require("stylelint")

const _ = require("lodash");
const mediaParser = require("postcss-media-query-parser");

var ruleName = "plugin/media-query-order"
const messages = stylelint.utils.ruleMessages(ruleName, {
    expected: selectorValue =>
        `Expected class selector "${selectorValue}" to match specified pattern`
});

module.exports = stylelint.createPlugin(ruleName, function(pattern, options) {
    return function(root, result) {

        var lastPixels = 0;
        root.walkAtRules(rule => {

            var pixels = check(rule);
            if (pixels <= lastPixels) {
                throw "Each next media query must describe larger width than previous: " + pixels
            }

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
                        resultArray.push(0);
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
            const result = mediaParser.default(rule.params);
            const pattern = mediaParser.default('screen and (min-width: 100px)');

            if (mediaToString(result) !== mediaToString(pattern)) {
                throw "Media query does not match pattern " + rule.params;
            }
            return getMediaPixels(result);
        }

    }
})

module.exports.ruleName = ruleName
module.exports.messages = messages
