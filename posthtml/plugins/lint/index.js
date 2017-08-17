'use strict';

module.exports = function (options) {
    if (!options) {
        options = {}
    }

    return function Lint (tree) {
        if (options.rules) {
            options.rules.forEach(function (definition) {
                const rule = require(definition.path)
                const config = definition.config ? definition.config : {}
                rule.run(tree, config, report)
            })
        }

        return tree
    }

    function report(message, node) {
        let header = options.file
        message = header + ': ' + message
        console.log(message)
    }
}
