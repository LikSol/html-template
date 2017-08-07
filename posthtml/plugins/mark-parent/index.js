'use strict';

module.exports = function (options) {
    if (!options) {
        options = {}
    }

    return function MarkParent (tree) {
        tree.walk(function (node) {
            for (var i in node.content) {
                var current = node.content[i]

                if (!current.tag) {
                    continue
                }

                current.parent = node;
            }

            return node;
        })

        return tree
    }
}
