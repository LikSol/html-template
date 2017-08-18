'use strict';

var path = require('path');

var ruleName = path.basename(__filename, '.js');

function validate(node) {
    if (hasContainer(node)) {
        return true;
    }

    var anyChildren = false;
    for (var i in node.content) {
        var child = node.content[i];
        if (child.tag) {
            anyChildren = true;
            if (!hasContainer(child)) {
                return false;
            }
        }
    }

    if (!anyChildren) {
        return false;
    }

    return true;

    function hasContainer(node) {
        if (!node.attrs.class) {
            return false;
        }

        var arrClass = node.attrs.class.split(/\s+/g);

        for(var i in arrClass) {
            var classValue = arrClass[i];
            if (classValue === 'container' || classValue === "container-fluid") {
                return true;
            }
        }

        return false;
    }

}

module.exports = {
    name: ruleName,
    run: function (tree, config_in, report) {
        tree.match({ tag: 'body' }, function (node) {
            let found = false
            node.content.forEach(function (child) {
                if (child.tag && child.tag !== 'script') {
                    found = true
                    if (!validate(child)) {
                        report({
                            ruleName: ruleName,
                            message: "One of 2 top level blocks must have container class. Absent for " + child.tag,
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

            return node;
        });
    }
}