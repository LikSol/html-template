'use strict';

var path = require('path');

var ruleName = path.basename(__filename, '.js');

function validate(node) {
    if (hasMeaningfulClass(node)) {
        return true;
    }

    return false;

    function hasMeaningfulClass(node) {
        if (!node.attrs.class) {
            return false;
        }

        var arrClass = node.attrs.class.split(/\s+/g);

        for(var i in arrClass) {
            var classValue = arrClass[i];
            if (/^(l|page)__[a-z]+/.test(classValue)) {
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
                        throw ruleName + ": Top level block must have meaningful class."
                        + " Absent for " + child.tag;
                        ;
                    }
                }
            }
            return node;
        });
    }
}