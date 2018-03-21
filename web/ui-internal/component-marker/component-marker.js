"use strict"

var ComponentMarker = (function () {
    var pub = {
        run: run,
    }

    return pub

    function run(config) {
        var Marker = {}

        function explain($box) {
            var boxOffset = $box.offset();
            var imgOffset = Marker.$img.offset();
            var coords = {
                x: Math.round((boxOffset.left - imgOffset.left) * Marker.img.proportion),
                y: Math.round((boxOffset.top - imgOffset.top) * Marker.img.proportion),
                w: Math.round(($box.outerWidth()) * Marker.img.proportion),
                h: Math.round(($box.outerHeight()) * Marker.img.proportion),
            }
            $box.find('.info').text(""
                + " X: " + coords.x
                + " Y: " + coords.y
                + " W: " + coords.w
                + " H: " + coords.h
            )
        }


        Marker.$board = $(config.boardSelector)
        Marker.$controls = $(config.controlsSelector)
        Marker.components = config.components
        Marker.$img = Marker.$board.children('img')

        Marker.$img.on('ComponentMarker.img.loaded', function () {
            Marker.img = {proportion: Marker.$img[0].naturalWidth / Marker.$img.width()}

            for (var componentSid in Marker.components) {
                var component = Marker.components[componentSid]

                for (var appearanceSid in component) {
                    var appearance = component[appearanceSid]
                    var css = {
                        top: Math.round(appearance.y / Marker.img.proportion),
                        left: Math.round(appearance.x / Marker.img.proportion),
                        width: Math.round((appearance.width ? appearance.width : (appearance.x1 - appearance.x)) / Marker.img.proportion),
                        height: Math.round((appearance.height ? appearance.height : (appearance.y1 - appearance.y)) / Marker.img.proportion),
                    }
                    var $marker =
                        $('<div id="component-' + componentSid + '-' + appearanceSid + '" data-type="component" data-sid="' + componentSid + '" data-id="' + appearanceSid + '"><div class="name">' + componentSid + '</div><div class="info"></div></div>').addClass('marker').css(css)


                    // if (appearance.show != 'auto') $marker.addClass('-hidden')

                    Marker.$board.append($marker)
                    $marker.draggable({
                        stop: function (event, ui) {
                            explain($(this))
                        }
                    })

                    $marker.resizable({
                        handles: "all",
                        stop: function (event, ui) {
                            explain($(this))
                        }
                    });

                }
            }

            Marker.$controls.find("[data-type='component']").on('mouseover', function () {
                var $control = $(this)
                Marker.$board.find("[data-sid='" + $control.data('sid') + "'][data-id='" + $control.data('id') + "']")
                    .addClass('-highlighted')
            })

            Marker.$controls.find("[data-type='component']").on('mouseout', function () {
                var $control = $(this)
                Marker.$board.find("[data-sid='" + $control.data('sid') + "'][data-id='" + $control.data('id') + "']")
                    .removeClass('-highlighted')
            })
            
            Marker.$img.on('click', function (e) {
                var offset = $(this).offset();
                var coords = {
                    x: e.pageX - offset.left,
                    y: e.pageY - offset.top,
                }

                console.log(coords)
            })
        })

        // фишка по load на img отсюда http://stackoverflow.com/a/13860853/1775065 НЕ РАБОТАЕТ
        // используем фишку отсюда https://stackoverflow.com/a/3877079/1775065
        Marker.$img.one("load", function() {
            Marker.$img.trigger('ComponentMarker.img.loaded');
        }).each(function () {
            if (this.complete) {
                $(this).trigger("load")
            }
        })

    }
})()
