"use strict"

class Appearance {
    constructor(config) {
        this.coords = config.coords

        this.board = config.board
        this.component = config.component

        this.callbacks = {}

        this.settings = {
            drag: config.drag,
            resize: config.resize,
        }
    }

    get $element() {
        if (!this._$element) {
            // var $marker =
            //     $('<div id="component-' + componentSid + '-' + appearanceSid + '" data-type="component" data-sid="' + componentSid + '" data-id="' + appearanceSid + '"><div class="name">' + componentSid + '</div><div class="info"></div></div>').addClass('marker').css(css)

            let $element =
                $(`<div><div class="name">${this.component.sid}</div><div class="info"></div></div>`)
                    .addClass('marker')
                    .hide()

            this.board.$preview.append($element)

            this.attachBehavior($element)

            this._$element = $element
        }

        return this._$element
    }

    destroy() {
        this.$element.remove()
        this._$element = null
    }

    reattach() {
        this.destroy()
        this.draw()
        this.triggerEvent('afterReattach')
    }

    draw() {
        let proportion = this.board.imgProportion

        var css = {
            top: Math.round(this.coords.y / proportion),
            left: Math.round(this.coords.x / proportion),
            width: Math.round(this.coords.width / proportion),
            height: Math.round(this.coords.height / proportion),
        }

        this.$element.css(css).show()
    }

    registerCallback(eventName, handlerName, callback) {
        if (!this.callbacks[eventName]) this.callbacks[eventName] = {}

        this.callbacks[eventName][handlerName] = callback
    }

    triggerEvent(eventName) {
        if (!this.callbacks[eventName]) return

        for (let key in this.callbacks[eventName]) {
            let callback = this.callbacks[eventName][key]

            callback(this)
        }
    }

    attachBehavior($element) {
        var self = this

        if (this.settings.drag) {
            $element.draggable({
                stop: function (event, ui) {
                    self.coords = self.elementCoords
                    self.triggerEvent('afterDrag')
                },
                containment: this.board.$preview,
                // cancel: '.info'
            })
        }

        if (this.settings.resize) {
            $element.resizable({
                handles: "all",
                stop: function (event, ui) {
                    self.coords = self.elementCoords
                    self.triggerEvent('afterResize')
                }
            })
        }

        $element.on('click', function () {
            self.triggerEvent('click')
        })
    }

    get elementCoords() {
        let $box = this.$element

        var boxOffset = $box.offset()
        var imgOffset = this.board.$img.offset()
        let proportion = this.board.imgProportion

        var coords = {
            x: Math.round((boxOffset.left - imgOffset.left) * proportion),
            y: Math.round((boxOffset.top - imgOffset.top) * proportion),
            width: Math.round(($box.outerWidth()) * proportion),
            height: Math.round(($box.outerHeight()) * proportion),
        }

        return coords
    }
}

class Component {
    constructor(config) {
        this.sid = config.sid
        this.comment = null
        this.hasRequirements = null
    }
}

class CutBoard {
    constructor(config) {
        this.$container = config.$container
        this.qid = config.qid

        this.appearances = []
    }

    get $preview() {
        if (!this._$preview) {
            let $preview = this.$container.children('[data-property=preview]')
            this._$preview = $preview
        }

        return this._$preview
    }

    initControls() {
        let $controls = this.$controls
        let self = this

        $controls.find('[data-property=size-original]').on('click', function (e) {
            e.preventDefault()

            let $preview = self.$preview

            self.$preview.toggleClass('-size-arbitrary')
            $preview.css('width', 'initial')

            self.drawAppearances()
        })

        $controls.find('[data-property=size-plus]').on('click', function (e) {
            e.preventDefault()

            let $preview = self.$preview

            $preview.css('width', $preview.width() * 1.25)
            $preview.addClass('-size-arbitrary')

            self.drawAppearances()
        })

        $controls.find('[data-property=size-minus]').on('click', function (e) {
            e.preventDefault()

            let $preview = self.$preview

            $preview.css('width', $preview.width() / 1.25)
            $preview.addClass('-size-arbitrary')

            self.drawAppearances()
        })

    }
    get $controls() {
        if (!this._$controls) {
            let $controls = this.$container.children('[data-property=controls]')

            this._$controls = $controls
        }

        return this._$controls
    }

    get $img() {
        if (!this._$img) this._$img = this.$preview.children('img')

        return this._$img
    }

    get imgNaturalInScreenPixels() {
        return this.imgProportion
    }

    get imgProportion() {
        let proportion = this.$img[0].naturalWidth / this.$img.width()
        return proportion
    }

    drawAppearances() {
        for (let key in this.appearances) {
            let appearance = this.appearances[key]

            appearance.draw()
        }
    }

    addAppearance(appearance) {
        this.appearances.push(appearance)

        appearance.draw()
    }

    removeAppearance(appearance) {
        let index = this.appearances.indexOf(appearance)

        if (index !== -1) {
            this.appearances.splice(index, 1);
        }

        appearance.destroy()
    }

    run() {
        let self = this

        self.$img.on('CutBoard.img.loaded', function () {
            self.drawAppearances()
            // Marker.$controls.find("[data-type='component']").on('mouseover', function () {
            //     var $control = $(this)
            //     Marker.$board.find("[data-sid='" + $control.data('sid') + "'][data-id='" + $control.data('id') + "']")
            //         .addClass('-highlighted')
            // })
            //
            // Marker.$controls.find("[data-type='component']").on('mouseout', function () {
            //     var $control = $(this)
            //     Marker.$board.find("[data-sid='" + $control.data('sid') + "'][data-id='" + $control.data('id') + "']")
            //         .removeClass('-highlighted')
            // })

        })

        // фишка по load на img отсюда http://stackoverflow.com/a/13860853/1775065 НЕ РАБОТАЕТ
        // используем фишку отсюда https://stackoverflow.com/a/3877079/1775065
        self.$img.one("load", function() {
            self.$img.trigger('CutBoard.img.loaded');
        }).each(function () {
            if (this.complete) {
                $(this).trigger("load")
            }
        })

        self.initControls()
    }

}

class AppearanceSetupStatus {
    constructor(config) {
        this.$container = config.$container
    }

    get count() {
        if (!this._count) this._count = 1

        return this._count++
    }

    setState(state, timeout, state2) {
        let glyph = "question-sign"
        let color = null

        switch (state) {
            case 'success':
                glyph = 'ok'
                color = 'success'
                break
            case 'error':
                glyph = 'exclamation-sign'
                color = 'danger'
                break
            case 'default':
                glyph = 'asterisk'
                color = null
                break
        }

        let newClass = "glyphicon glyphicon-" + glyph + (color ? ' text-' + color : '')

        this.$container.prop('class', newClass)
        let count = this.count
        this.$container.data('count', count)

        if (state2) {
            let self = this

            setTimeout(function() {
                    if (self.$container.data('count') != count) return false;

                    self.setState(state2)
                },
                timeout
            );
        }

    }
}

class AppearanceList {
    constructor(config) {
        this.$container = config.$container
        this.callbacks = {}
    }

    registerCallback(eventName, handlerName, callback) {
        if (!this.callbacks[eventName]) this.callbacks[eventName] = {}

        this.callbacks[eventName][handlerName] = callback
    }

    triggerEvent(eventName, data) {
        if (!this.callbacks[eventName]) return

        for (let key in this.callbacks[eventName]) {
            let callback = this.callbacks[eventName][key]

            callback(this, data)
        }
    }

    build(manager) {
        let self = this

        for (let componentSid in manager.components) {
            let appearances = []
            for (let appearance of manager.appearances()) {
                if (appearance.component.sid !== componentSid) continue

                appearances.push(appearance)
            }

            let $component = $(`<div></div>`)
            let $componentDetailLink = $(`
                <a class="componentDetailLink" href="/project/show?projectName=${manager.projectSid}#component-${componentSid}"><span class="glyphicon glyphicon-question-sign"></span></a>
            `)

            if (manager.components[componentSid].comment) {
                $componentDetailLink.append('<span style="margin-left: 5px; color: #e58b00" class="glyphicon glyphicon-comment"></span>')
            }

            if (manager.components[componentSid].hasRequirements) {
                $componentDetailLink.append('<span style="margin-left: 5px; color: #af3fee" class="glyphicon glyphicon-exclamation-sign"></span>')
            }

            let $componentHeading = $(`<div class="heading">${componentSid}</div>`)

            let $wrapper

            switch (true) {
                case appearances.length == 0:
                    $componentHeading.append($componentDetailLink)
                    $component.append($componentHeading)
                    break
                case appearances.length == 1:
                    // $component.append($(`<div class="heading">${componentSid}</div>`))
                    $wrapper = $('<div></div>')
                    let $appearance = $(`<a href="#" class="appearanceItem">${componentSid}</a>`)
                    $appearance[0].appearance = appearances[0]
                    $wrapper.append($appearance)
                    $wrapper.append($componentDetailLink)
                    $component.append($wrapper)
                    break
                default:
                    $componentHeading.append($componentDetailLink)
                    $component.append($componentHeading)
                    $wrapper = $(`<div class="list"></div>`)
                    for (let key in appearances) {
                        let $appearance = $(`<a href="#" class="appearanceItem">${key * 1 + 1}</a>`)
                        $appearance[0].appearance = appearances[key]
                        $wrapper.append($appearance)
                    }
                    $component.append($wrapper)
                    break

            }

            this.$container.append($component)

            $component.find('.appearanceItem').on('mouseenter', (function () {
                return function () {
                    self.triggerEvent('mouseenter', {appearance: $(this)[0].appearance})
                }
            })())
            $component.find('.appearanceItem').on('mouseleave', (function () {
                return function () {
                    self.triggerEvent('mouseleave', {appearance: $(this)[0].appearance})
                }
            })())
            $component.find('.appearanceItem').on('click', (function () {
                return function () {
                    self.triggerEvent('click', {appearance: $(this)[0].appearance})
                }
            })())
        }
    }

}

class AppearanceSetup {
    constructor(config) {
        this.$container = config.$container
    }

    get $componentSelect() {
        if (!this._$componentSelect) {
            this._$componentSelect = this.$container.find('[data-property=component]')
        }

        return this._$componentSelect
    }

    get $save() {
        if (!this._$save) {
            this._$save = this.$container.find('[data-property=save]')
        }

        return this._$save
    }

    get $delete() {
        if (!this._$delete) {
            this._$delete = this.$container.find('[data-property=delete]')
        }

        return this._$delete
    }

    get $saveConfig() {
        if (!this._$saveConfig) {
            this._$saveConfig = this.$container.find('[data-property=save-config]')
        }

        return this._$saveConfig
    }

    get $addComponent() {
        if (!this._$addComponent) {
            this._$addComponent = this.$container.find('[data-property=add-component]')
        }

        return this._$addComponent
    }

    get $addComponentName() {
        if (!this._$addComponentName) {
            this._$addComponentName = this.$container.find('[data-property=add-component-name]')
        }

        return this._$addComponentName
    }

    get status () {
        if (!this._status) {
            this._status = new AppearanceSetupStatus({
                $container: this.$container.find('[data-property=status]')
            })
        }

        return this._status
    }
}

class CutBoardManager {
    constructor(config) {
        this.projectSid = config.projectSid

        this.$workspace = null
        this.boards = {}
        this.components = {}

        this.settings = {
            drag: true,
            resize: true,
        }

        for (let key in this.settings) {
            if (typeof config[key] !== "undefined") {
                this.settings[key] = config[key]
            }
        }
    }

    setWorkspace($workspace) {
        this.$workspace = $workspace
    }

    getBoard(qid) {
        if (!this.boards[qid]) {
            let board = new CutBoard({
                $container: this.$workspace.find('[data-property=previews]').children('[data-object=board][data-qid="' + qid + '"]'),
                qid: qid
            })

            this.boards[qid] = board
        }

        return this.boards[qid]
    }

    applyComponentsConfig(boardQid, appearencesConfig, componentsConfig) {
        let board = this.getBoard(boardQid)

        for (var componentSid in appearencesConfig) {
            var componentConfig = appearencesConfig[componentSid]

            var component = new Component({
                sid: componentSid,
            })

            for (var appearanceSid in componentConfig) {
                var appearanceConfig = componentConfig[appearanceSid]

                this.addAppearance(board, appearanceConfig, component)
            }

            this.components[componentSid] = component
        }

        for (let componentSid in componentsConfig) {
            if (!this.components[componentSid]) continue

            this.components[componentSid].comment = componentsConfig[componentSid].comment
            this.components[componentSid].hasRequirements = componentsConfig[componentSid].hasRequirements
        }
    }

    get nonameComponent() {
        if (!this._nonameComponent) {
            let component = new Component({
                sid: '[NoName]'
            })
            this._nonameComponent = component
        }

        return this._nonameComponent
    }

    addAppearance(board, coords, component) {
        if (!component) component = this.nonameComponent

        let appearance = new Appearance({
            board: board,
            component: component,
            coords: coords,
            drag: this.settings.drag,
            resize: this.settings.resize,
        })

        this.attachAppearanceBehavior(appearance)

        board.addAppearance(appearance)

        return appearance
    }

    get appearanceSetup() {
        if (!this._appearanceSetup) {
            this._appearanceSetup = new AppearanceSetup({
                $container: this.$workspace.children('[data-property=appearance-setup]')
            })
            this._appearanceSetup.$container.draggable({})
        }

        return this._appearanceSetup
    }

    get appearanceList() {
        if (!this._appearanceList) {
            this._appearanceList = new AppearanceList({
                $container: this.$workspace.find('[data-property=appearance-list]')
            })
        }

        return this._appearanceList
    }

    *appearances() {
        for (let key in this.boards) {
            let board = this.boards[key]

            for (let akey in board.appearances) {
                let appearance = board.appearances[akey]

                yield appearance
            }
        }
    }

    selectAppearance(appearance) {
        this.appearanceSetup.$componentSelect
            .find('[value="' + appearance.component.sid + '"]')
            .prop('selected', true)

        for (let appearance of this.appearances()) {
            appearance.$element.removeClass('-highlighted')
        }

        appearance.$element.addClass('-highlighted')

        this.selectedAppearance = appearance
    }

    toggleHighlightAppearance(appearance, state) {
        appearance.$element.toggleClass('-highlighted', state)
    }

    attachAppearanceBehavior(appearance) {
        let self = this

        appearance.registerCallback('click', 'CutBoardManager.click', function (appearance) {
            self.selectAppearance(appearance)
        })

        appearance.registerCallback('afterResize', 'CutBoardManager.selectAppearance', function (appearance) {
            self.selectAppearance(appearance)
        })

        appearance.registerCallback('afterDrag', 'CutBoardManager.selectAppearance', function (appearance) {
            self.selectAppearance(appearance)
        })

        appearance.registerCallback('afterReattach', 'CutBoardManager.restoreElementBehavior', function (appearance) {
            appearance.$element.on('click', function () {
                self.selectAppearance(appearance)
            })
        })
    }

    fillComponentsList() {
        let $componentSelect = this.appearanceSetup.$componentSelect
        $componentSelect.html('')

        for (let componentSid in this.components) {
            $componentSelect
                .append($("<option></option>")
                    .attr("value", componentSid)
                    .text(componentSid));
        }
    }

    initAppearanceSetup() {
        let self = this

        let appearanceSetup = this.appearanceSetup

        let components = {}

        for (let componentSid in this.components) {
            let component = this.components[componentSid]
            components[component.sid] = component
        }

        this.fillComponentsList()

        appearanceSetup.$save.on('click', function () {
            let appearance = self.selectedAppearance
            if (!appearance) {
                alert('Не выбран элемент')
                return
            }

            let componentSid = self.appearanceSetup.$componentSelect.val()
            appearance.component = self.components[componentSid]
            appearance.reattach()
        })

        appearanceSetup.$delete.on('click', function () {
            let appearance = self.selectedAppearance
            if (!appearance) {
                alert('Не выбран элемент')
                return
            }

            appearance.board.removeAppearance(appearance)
        })

        appearanceSetup.$saveConfig.on('click', function () {
            self.saveConfig()
        })

        appearanceSetup.$addComponent.on('click', function () {
            let componentName = appearanceSetup.$addComponentName.val()
            if (!componentName.trim()) {
                alert('No name')
                return
            }

            for (let componentSid in self.components) {
                if (componentSid == componentName) {
                    alert('Component already exists')
                    return
                }
            }

            let component = new Component({
                sid: componentName
            })

            self.components[component.sid] = component

            self.fillComponentsList()
            self.appearanceSetup.$componentSelect
                .find('[value="' + component.sid + '"]')
                .prop('selected', true)

            appearanceSetup.$addComponentName.val('')

            self.appearanceSetup.status.setState('success', 2000, 'default')
        })
    }

    saveConfig() {
        let self = this

        let config = {
            appearances: [],
            previews: [],
            components: []
        }
        let hasNonameComponent = false

        for (let appearance of this.appearances()) {
            let component = appearance.component

            if (component === this.nonameComponent) {
                hasNonameComponent = true
                continue
            }

            let element = {
                coords: appearance.coords,
                componentSid: component.sid,
                previewQid: appearance.board.qid
            }

            config.appearances.push(element)
        }

        if (hasNonameComponent) {
            alert('There is noname component')
            return
        }

        for (let componentSid in this.components) {
            config.components.push(componentSid)
        }

        for (let boardQid in this.boards) {
            config.previews.push(boardQid)
        }

        $.post('/project/save-cut-config', {
            config: JSON.stringify(config),
            projectSid: this.projectSid,
        })
            .done(function () {
                self.appearanceSetup.status.setState('success', 2000, 'default')
            })
            .fail(function () {
                self.appearanceSetup.status.setState('error')
                alert('ОШИБКА: Данные не сохранены');
            })
    }

    initAppearanceList() {
        let self = this

        let appearanceList = this.appearanceList

        let $container = appearanceList.$container
        if (!$container.length) return

        appearanceList.build(this)

        appearanceList.registerCallback('mouseenter', 'CutBoardManager.appearanceEnter', function (appearanceList, data) {
            self.toggleHighlightAppearance(data.appearance, true)
        })
        appearanceList.registerCallback('mouseleave', 'CutBoardManager.appearanceLeave', function (appearanceList, data) {
            self.toggleHighlightAppearance(data.appearance, false)
        })
        appearanceList.registerCallback('click', 'CutBoardManager.appearanceClick', function (appearanceList, data) {
                $('html, body').animate({
                    scrollTop: data.appearance.$element.offset().top
                }, 200);

            // self.toggleHighlightAppearance(data.appearance, false)
        })
    }

    run() {
        let self = this

        for (let key in this.boards) {
            let board = this.boards[key]

            board.run()
        }

        for (let key in this.boards) {
            let board = this.boards[key]

            board.$img.on('dblclick', (function(board) {
                return function (e) {
                    let width = 50
                    let height = 50

                    let coords = {
                        x: Math.round(e.offsetX * board.imgNaturalInScreenPixels),
                        y: Math.round(e.offsetY * board.imgNaturalInScreenPixels),
                        width: Math.round(width * board.imgNaturalInScreenPixels),
                        height: Math.round(height * board.imgNaturalInScreenPixels),
                    }

                    let appearance = self.addAppearance(board, coords)

                    self.selectAppearance(appearance)
                }

            })(board))
        }

        this.initAppearanceSetup()
        this.initAppearanceList()

    }

}



