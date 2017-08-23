'use strict'

var path = require('path')

var ruleName = path.basename(__filename, '.js')

function hasContainer(node) {
    if (!node.attrs || !node.attrs.class) return false

    var arrClass = node.attrs.class.split(/\s+/g)

    for(var i in arrClass) {
        var classValue = arrClass[i]
        if (classValue === 'container' || classValue === "container-fluid") return true
    }

    return false
}

function validate(node) {
    if (hasContainer(node)) return true

    var anyChildren = false
    for (var i in node.content) {
        var child = node.content[i]
        if (child.tag) {
            anyChildren = true
            if (!hasContainer(child)) return false
        }
    }

    return anyChildren
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

            return node
        })
            .walk(function (node) {
                if (hasContainer(node)) {
                    if (!isContainerInPlace(node)) {
                        report({
                            ruleName: ruleName,
                            message: "Container must be used not deeper than first 2 levels from body.",
                            raw: {tag: node.tag, attrs: node.attrs}
                        })
                    }
                }

                return node

                function isContainerInPlace(node) {
                    if (!node.parent) return false // would be strange
                    if (node.parent.tag === 'body') return true
                    if (node.parent.parent && node.parent.parent.tag === 'body') return true
                    return false
                }
            })
    }
}