'use strict';

var gutil = require("gulp-util");
var log = gutil.log;
var col = gutil.colors;

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
        log(col.yellow(message))

        if (data.raw) {
            log(data.raw)
        }
    }
}
