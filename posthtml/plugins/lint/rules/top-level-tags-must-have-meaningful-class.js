'use strict'

const path = require('path')
const _ = require("lodash")

var ruleName = path.basename(__filename, '.js');

function validate(node, patterns) {

    if (hasMeaningfulClass(node, patterns)) {
        return true;
    }

    return false;

    function hasMeaningfulClass(node, patterns) {
        if (!node.attrs.class) {
            return false;
        }

        const arrClass = node.attrs.class.split(/\s+/g);

        return patterns.some(function (pattern) {
            return arrClass.some(function (tag_class) {
                return tag_class.match(pattern.pattern)
            })
        })
    }

}

module.exports = {
    name: ruleName,
    run: function (tree, config, report) {
        let patterns = []

        config.patterns.forEach(function (pattern) {
            const normalizedPattern = _.isString(pattern.pattern)
                ? new RegExp('^' + pattern.pattern + '$')
                : pattern.pattern

            let new_pattern = _.clone(pattern)
            new_pattern.pattern = normalizedPattern
            patterns.push(new_pattern)
        })

        let roots = []
        if (config.level) {
            tree.match({tag: config.level}, function (node) {
                roots.push(node.content)
                return node
            })
        } else {
            roots.push(tree)
        }

        roots.forEach(function (root) {
            let found = false
            root.forEach(function (child) {
                if (child.tag && child.tag !== 'script') {
                    found = true
                    if (!validate(child, patterns)) {
                        report({
                            ruleName: ruleName,
                            message: "Top level block must have meaningful class. Absent for " + child.tag,
                            raw: {tag: child.tag, attrs: child.attrs}
                        })
                    }
                }
            })

            if (!found) {
                report({
                    ruleName: ruleName,
                    message: "No children found in body",
                })
            }

        })


        return tree
    }
}