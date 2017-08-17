'use strict';

const path = require('path')
const { URL }= require('url')
const _ = require("lodash")

const ruleName = path.basename(__filename, '.js');

module.exports = {
    name: ruleName,
    run: function (tree, config_in, report) {
        let config = _.clone(config_in)

        tree.match([{ tag: 'script' }, {tag: 'link'}], function (node) {
            let node_url

            let matched = config.find(function (pattern) {
                if (pattern.tag !== node.tag) return false

                switch (pattern.tag) {
                    case 'script':
                        node_url = node.attrs.src
                        // разрешаем (в этом плагине) скрипты без src (инлайновые)
                        if (node_url === '') return true
                        break
                    case 'link':
                        node_url = node.attrs.href
                        break
                }

                let node_abs_url = new URL(node_url, 'http://local')
                node_abs_url.search = ''
                let pattern_abs_url = new URL(pattern.url, 'http://local')

                let result = node_abs_url.toString() === pattern_abs_url.toString()

                return result
            })

            if (!matched) {
                report("Not allowed library " + node_url, node)
            }

            return node
        })
    }
}