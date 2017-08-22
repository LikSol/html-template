"use strict"

$(document).ready(function() {
    var $show_success = $('[data-object=show-success]')
    var $show_errors = $('[data-object=show-errors]')
    var $show_initial = $('[data-object=show-initial]')

    $show_success.on('click', function () {
        $('.form-group').each(function () {
            var $group = $(this)

            reset($group)

            $group.addClass('has-success')
        })
    })

    $show_errors.on('click', function () {
        $('.form-group').each(function () {
            var $group = $(this)

            reset($group)

            $group.addClass('has-error')
            $group.children('.help-block').text('текст ошибки')

        })
    })

    $show_initial.on('click', function () {
        $('.form-group').each(function () {
            var $group = $(this)

            reset($group)
        })
    })

    function reset($group) {
        $group.removeClass('has-error')
        $group.removeClass('has-success')
        $group.children('.help-block').text('')
    }

})