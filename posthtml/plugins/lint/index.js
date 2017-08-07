'use strict';

module.exports = function (options) {
    if (!options) {
        options = {}
    }

    return function Lint (tree) {
        if (options.rules) {
            for (var i in options.rules) {
                var rulePath = options.rules[i]

                var rule = require(rulePath)
                rule.run(tree);
            }
        }

        return tree
    }
}
