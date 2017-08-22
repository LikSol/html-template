'use strict'

module.exports = {
    global: {
        pages: [
            'example',
            'example-forms',
        ],
        domain: null,
    },
    'lint-html-real': {
        lint: {
            mandatory: [
                { url: 'https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css', tag: 'link'},
                { url: 'https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js', tag: 'script' },
                { url: 'https://code.jquery.com/jquery-3.2.1.min.js', tag: 'script' },
            ],
            noAllowedCheckOn: [
                'example-forms'
            ],
        }
    },
    'lint-git': {
        allow: [
            /^web\/frontend\//,
            /^apps\/main\/views\/layouts\/layout\.php$/,
            /^apps\/main\/views\/template\/[a-z]+\.php$/,
        ],
        sha: 'be3391e'
    },
    'build-pages': {
        scheme: 'http'
    },
    'lint-css': {
        parts: {
            layout: {
                stylelint: {
                    plugins: [
                        __dirname + "/stylelint/top-selector-match-pattern",
                    ],
                    rules: {
                        "plugin/top-selector-match-pattern": {
                            patterns: [
                                {
                                    type: 'class',
                                    name: '.l__*',
                                    pattern: "l__[a-z]+"
                                },
                                {
                                    type: 'tag',
                                    name: 'body',
                                    pattern: "body"
                                },
                            ]
                        },
                    }
                }
            },
            component: {
                stylelint: {
                    plugins: [
                        __dirname + "/stylelint/top-selector-match-pattern",
                    ],
                    rules: {
                        "plugin/top-selector-match-pattern": {
                            patterns: [
                                {
                                    type: 'class',
                                    name: '.c__*',
                                    pattern: "c__[a-z0-9-]+"
                                },
                            ]
                        },
                    }
                }
            }
        }
    }
}