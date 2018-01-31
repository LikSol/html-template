"use strict"

$(function () {
    ComponentMarker.$board = $(ComponentMarker.boardSelector)
    ComponentMarker.$img = ComponentMarker.$board.children('img')

    ComponentMarker.img = {proportion: ComponentMarker.$img[0].naturalWidth / ComponentMarker.$img.width()}

    for (var componentSid in ComponentMarker.components) {
        var component = ComponentMarker.components[componentSid]

        for (var appearanceSid in component) {
            var appearance = component[appearanceSid]
            var css = {
                top: appearance.y / ComponentMarker.img.proportion,
                left: appearance.x / ComponentMarker.img.proportion,
                width: (appearance.x1 - appearance.x) / ComponentMarker.img.proportion,
                height: (appearance.y1 - appearance.y) / ComponentMarker.img.proportion
            }
            var $marker = $('<div>' + componentSid + '</div>').addClass('marker').css(css)

            ComponentMarker.$board.append($marker)
        }
    }

})