'use strict';

const path = require('path')
const { URL }= require('url')
const _ = require("lodash")

const ruleName = path.basename(__filename, '.js');

module.exports = {
    name: ruleName,
    run: function (tree, config_in, report) {
        let regexp = config_in

        tree.match([{ tag: 'img' }], function (node) {
            if (!node.attrs.src.match(regexp)) {
                report("Image src " + node.attrs.src + " does not match regexp " + regexp)
            }

            return node
        })
    }
}