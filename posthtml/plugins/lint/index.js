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

    function report(data) {
        let info = {ruleName: null, message: null}
        if (typeof data === "object") {
            info.ruleName = data.ruleName
            info.message = data.message
        } else{
            info.message = data
        }

        if (!info.message) {
            throw new Error("Message not specified")
        }
        if (!info.ruleName) {
            info.ruleName = 'Unknown'
        }

        let message = `${options.file}: ${info.ruleName}: ${info.message}`
        console.log(message)

        if (data.raw) {
            console.log(data.raw)
        }
    }
}
