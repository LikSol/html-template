"use strict"

$(function () {
    var $demoButton = $('[data-object=demo-page-features-js-button]')

    $demoButton.on('click', function (e) {
        e.preventDefault()

        alert('Вы нажали на кнопку')
    })
})