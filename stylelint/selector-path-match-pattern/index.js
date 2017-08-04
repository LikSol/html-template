// Abbreviated example
var stylelint = require("stylelint")

const _ = require("lodash");
const isKeyframeSelector = require("stylelint/lib/utils/isKeyframeSelector");
const isStandardSyntaxRule = require("stylelint/lib/utils/isStandardSyntaxRule");
const isStandardSyntaxSelector = require("stylelint/lib/utils/isStandardSyntaxSelector");
const parseSelector = require("stylelint/lib/utils/parseSelector");
const report = require("stylelint/lib/utils/report");


var ruleName = "plugin/selector-path-match-pattern"
const messages = stylelint.utils.ruleMessages(ruleName, {
    expected: selectorValue =>
        `Expected class selector "${selectorValue}" to match specified pattern`
});

module.exports = stylelint.createPlugin(ruleName, function(pattern, options) {
    options = options || {};
    return function(root, result) {
        var validOptions = stylelint.utils.validateOptions(
            result,
            ruleName,
            {
                actual: pattern,
                possible: [_.isRegExp, _.isString]
            }
        );
        if (!validOptions) { return }

        const normalizedPattern = _.isString(pattern)
            ? new RegExp(pattern)
            : pattern;

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
            // parseSelector('.l__*', result, rule, s => checkSelector(s, rule));
            // process.exit();
            function checkSelector(fullSelector, rule) {

                fullSelector.each((selector, index) => {
                    const value = selector.toString();
                    const sourceIndex = selector.sourceIndex;

                    if (normalizedPattern.test(value)) {
                        return;
                    }
                    report({
                        result,
                        ruleName,
                        // message: "OH",
                        message: messages.expected(value),
                        node: rule,
                        index: sourceIndex
                    });
                });

            }

            // process.exit();

            // stylelint.utils.report({
            //     result,
            //     ruleName,
            //     message: messages.expected(selector),
            //     node: rule,
            //     index: sourceIndex
            // });
        });
    }
})

module.exports.ruleName = ruleName
module.exports.messages = messages
