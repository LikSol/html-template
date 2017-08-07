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
    run: function (tree) {
        tree.match({ tag: 'body' }, function (node) {
            for (var i in node.content) {
                var child = node.content[i];
                if (child.tag && child.tag !== 'script') {
                    if (!validate(child)) {
                        throw ruleName + ": One of 2 top level blocks must have container class."
                            + " Absent for " + child.tag;
                    }
                }
            }
            return node;
        });
    }
}