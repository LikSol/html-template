'use strict';

const path = require('path')
const _ = require("lodash")

const ruleName = path.basename(__filename, '.js');
let patterns = require('./html-fixed-input-structure/patterns.js')

patterns.push({tag: 'input', attrs: {name: '_csrf', type: 'hidden'}})

module.exports = {
    name: ruleName,
    run: function (tree, config_in, report) {
        let config = _.clone(config_in)

        const match = tree.match

        tree.match([{tag: 'div'}, {tag: 'input'}], function (node) {
            if (patterns.some(pattern => isNodeMatchesPattern(node, pattern))) {
                if (node.tag === 'input') {
                    node._structureCorrect = true
                }
                match.call(node, [{tag: 'input'}, {tag: 'select'}], function (input) {
                    input._structureCorrect = true
                    return input
                })
            }
            // console.log('eee')
            // process.exit()

            return node
        })
            .match([{tag: 'input'}, {tag: 'select'}], function (input) {
                if (!input._structureCorrect) {
                    report({
                        ruleName: ruleName,
                        message: "Input does not match any pattern.",
                        raw: {tag: input.tag, attrs: input.attrs}
                    })
                }

                return input
            })

        return tree

        function isNodeMatchesPattern(node, pattern) {
            // if (node.tag === 'input' && node.attrs.type === 'hidden')
            //     console.log(node.tag, node.attrs.type, pattern.tag)
            // console.log(pattern)
            if (node.tag !== pattern.tag) return false
            if (pattern.class) {
                if (!node.attrs) return false
                if (!node.attrs.class) return false

                // console.log('P', pattern.class, node.attrs.class)
                if (node.attrs.class.split(/ /).indexOf(pattern.class) === -1) return false
            }
            if (pattern.attrs) {
                if (!node.attrs) return false
                const attrs_matched = Object.keys(pattern.attrs).every(function (key) {
                    if (!node.attrs[key]) return false
                    if (node.attrs[key] !== pattern.attrs[key]) return false

                    return true
                })
                if (!attrs_matched) return false
            }
            if (pattern.content) {
                if (!node.content) return false

                let i = 0
                let children_matched = node.content.every(function (child) {
                    // whitespace, разрешаем
                    if (!child.tag) return true
                    // разрешаем также hidden input, если они и правда были разрешены
                    if (pattern.hiddenInputAllowed && child.attrs && child.attrs.type === "hidden") return true

                    if (!pattern.content[i]) return false
                    if (!isNodeMatchesPattern(child, pattern.content[i])) return false // в pattern меньше элементов, чем в node

                    i++

                    return true
                })

                if (!children_matched) return false
                if (i !== pattern.content.length) return false // в node меньше элементов, чем в pattern
            }

            return true
        }
    }
}