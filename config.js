
const merge = require('merge-deep');
const fs = require('fs');
const readYaml = require('read-yaml')

const config = readYaml.sync('./lint-config.yaml')

let localConfig = {};

if (fs.existsSync('./lint-config-local.yaml')) {
    localConfig = readYaml.sync('./lint-config-local.yaml')
    config.global = merge(config.global, localConfig.global)
}

module.exports = {
    getForTask: function (taskName, taskDefaults) {
        return merge(config[taskName], taskDefaults, localConfig[taskName])
    },
    get: function () {
        return config
    }
}

