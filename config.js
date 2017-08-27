
const merge = require('merge-deep');
const fs = require('fs');
const readYaml = require('read-yaml')

const config = readYaml.sync('./lint-config.yaml')

let localConfig = fs.existsSync('./lint-config-local.yaml')
    ? readYaml.sync('./lint-config-local.yaml')
    : {}

module.exports = {
    getForTask: function (taskName, taskDefaults) {
        const taskConfig = merge(
            {global: config.global},
            config[taskName],

            taskDefaults,

            localConfig[taskName],
            {global: localConfig.global}
        )
        return taskConfig
    },
    get: function () {
        return merge(config, localConfig)
    }
}

