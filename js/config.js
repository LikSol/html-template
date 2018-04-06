"use strict"

const merge = require('merge-deep');
const fs = require('fs');
const readYaml = require('read-yaml')

class Page {
    init(data) {
        this.definition = data
    }
}

class Review {
    constructor(configObject) {
        this.configObject = configObject
    }

    getPagesByWidths() {
        let config = this.configObject

        let resolutions = {}

        for (let pageSid in config.pages) {
            let page = config.pages[pageSid]
            if (typeof page.definition.review !== "undefined" && !page.definition.review) continue

            for (let widthSid in page.definition.widths) {
                let width = page.definition.widths[widthSid]

                if (typeof width.review !== "undefined" && !width.review) continue

                if (!resolutions[width.width]) resolutions[width.width] = {}

                resolutions[width.width][pageSid] = page
            }
        }

        return resolutions
    }

}


class Config {
    constructor() {
        this.settings = {
            projectSid: null,
        }
        this.paths = {}

        this.global = {
            scheme: 'http',
            domain: 'new.dev.loc',
        }
    }

    setRoot(root) {
        this.paths.root = root
    }

    setProject(projectSid) {
        this.settings.projectSid = projectSid
    }

    get reviewDir() {
        return `${this.paths.root}/projects/${this.settings.projectSid}/review`
    }

    get reviewVersion() {
        return 'v1'
    }

    get Review() {
        if (!this._Review) {
            this._Review = new Review(this)
        }

        return this._Review
    }

    get rawConfig() {
        if (!this._rawConfig) {
            const configDir = `${this.paths.root}/projects/${this.settings.projectSid}/config`
            let config = readYaml.sync(configDir + '/config.yaml')

            for (let key in config.imports) {
                let importConfig = config.imports[key]
                let part = readYaml.sync(configDir + '/' + importConfig.resource)
                config = merge(config, part)
            }

            this._rawConfig = config
        }

        return this._rawConfig
    }

    get pages() {
        if (!this._pages) {
            this._pages = {}
            for (let pageSid in this.rawConfig.tasks.pages) {
                let page = new Page();
                page.init(this.rawConfig.tasks.pages[pageSid])
                this._pages[pageSid] = page
            }
        }

        return this._pages
    }


}

module.exports = new Config()

