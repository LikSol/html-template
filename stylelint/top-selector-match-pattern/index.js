// Abbreviated example
var stylelint = require("stylelint")

const _ = require("lodash");
const isKeyframeSelector = require("stylelint/lib/utils/isKeyframeSelector");
const isStandardSyntaxRule = require("stylelint/lib/utils/isStandardSyntaxRule");
const isStandardSyntaxSelector = require("stylelint/lib/utils/isStandardSyntaxSelector");
const parseSelector = require("stylelint/lib/utils/parseSelector");
const report = require("stylelint/lib/utils/report");


var ruleName = "plugin/top-selector-match-pattern"
const messages = stylelint.utils.ruleMessages(ruleName, {
    expected: function (selectorValue, patterns) {
        if (patterns.length === 1) {
            pattern = patterns[0]
            return `Expected selector "${selectorValue}" to match pattern "${pattern.type} ${pattern.name}"`
        } else {
            let patterns_list = patterns.reduce(function (carry, pattern) {
                carry.push('"' + pattern.type + ' ' + pattern.name + '"')
                return carry
            }, []).join(", ")

            return `Expected selector "${selectorValue}" to match at least one pattern: ${patterns_list}`
        }
    }
});

module.exports = stylelint.createPlugin(ruleName, function(config, options) {
    options = options || {};
    return function(root, result) {
        const validOptions = stylelint.utils.validateOptions(
            result,
            ruleName,
            {
                actual: config,
                possible: {
                    patterns: function (value) {
                        return typeof value === "object"
                    }
                }
            }
        );
        if (!validOptions) { return }

        let patterns = []

        config.patterns.forEach(function (pattern) {
            const normalizedPattern = _.isString(pattern.pattern)
                ? new RegExp('^' + pattern.pattern + '$')
                : pattern.pattern

            let new_pattern = _.clone(pattern)
            new_pattern.pattern = normalizedPattern
            patterns.push(new_pattern)
        })

        root.walkRules(rule => {
            const selector = rule.selector;
            const selectors = rule.selectors;

            if (!isStandardSyntaxRule(rule)) {
                return;
            }
            if (!isStandardSyntaxSelector(selector)) {
                return;
            }
            if (selectors.some(s => isKeyframeSelector(s))) {
                return;
            }

            parseSelector(selector, result, rule, s => checkSelector(s, rule));

            function checkSelector(fullSelector, rule) {
                fullSelector.each((selector) => {
                    const first = selector.nodes[0]

                    const sourceIndex = selector.sourceIndex;

                    const matched = patterns.some(function (pattern) {
                        if (!first.type == pattern.type) return false
                        if (!first.value.match(pattern.pattern)) return false
                        return true
                    })

                    if (matched) return

                    report({
                        result,
                        ruleName,
                        message: messages.expected(first.toString(), patterns),
                        node: rule,
                        index: sourceIndex
                    });
                });

            }
        });
    }
})

module.exports.ruleName = ruleName
module.exports.messages = messages
