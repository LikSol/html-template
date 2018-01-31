"use strict"

$(function () {
    ComponentMarker.$board = $(ComponentMarker.boardSelector)
    ComponentMarker.$controls = $(ComponentMarker.controlsSelector)
    ComponentMarker.$img = ComponentMarker.$board.children('img')

    ComponentMarker.img = {proportion: ComponentMarker.$img[0].naturalWidth / ComponentMarker.$img.width()}

    for (var componentSid in ComponentMarker.components) {
        var component = ComponentMarker.components[componentSid]

        for (var appearanceSid in component) {
            var appearance = component[appearanceSid]
            var css = {
                top: appearance.y / ComponentMarker.img.proportion,
                left: appearance.x / ComponentMarker.img.proportion,
                width: appearance.width ? appearance.width : (appearance.x1 - appearance.x) / ComponentMarker.img.proportion,
                height: appearance.height ? appearance.height : (appearance.y1 - appearance.y) / ComponentMarker.img.proportion,
            }
            var $marker = $('<div data-type="component" data-sid="' + componentSid + '">' + componentSid + '</div>').addClass('marker').css(css)

            if (appearance.show != 'auto') $marker.addClass('-hidden')

            ComponentMarker.$board.append($marker)
        }
    }

    ComponentMarker.$controls.find("[data-type='component']").on('mouseover', function () {
        var $control = $(this)
        ComponentMarker.$board.find("[data-sid='" + $control.data('sid') + "']").addClass('-highlighted')
    })

    ComponentMarker.$controls.find("[data-type='component']").on('mouseout', function () {
        var $control = $(this)
        ComponentMarker.$board.find("[data-sid='" + $control.data('sid') + "']").removeClass('-highlighted')
    })



})